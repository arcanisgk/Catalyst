<?php

declare(strict_types=1);

/**************************************************************************************
 *
 * Catalyst PHP Framework
 * PHP Version 8.3 (Required).
 *
 * @package   Catalyst
 * @subpackage Assets
 * @see       https://github.com/arcanisgk/catalyst
 *
 * @author    Walter Nuñez (arcanisgk/original founder) <icarosnet@gmail.com>
 * @copyright 2023 - 2025
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 *
 * @note      This program is distributed in the hope that it will be useful
 *            WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 *            or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @category  Framework
 * @filesource
 *
 * @link      https://catalyst.dock Local development URL
 *
 * BasicAuthMiddleware component for the Catalyst Framework
 *
 */

namespace Catalyst\Framework\Core\Middleware;

use Catalyst\Framework\Core\Http\Request;
use Catalyst\Framework\Core\Response\Response;
use Closure;
use Exception;

class BasicAuthMiddleware extends CoreMiddleware
{
    // Configuration
    private const int MAX_ATTEMPTS = 3;
    private const int ATTEMPT_DELAY = 30; // seconds
    private const int LOCKOUT_TIME = 1800; // 30 minutes in seconds
    private const string STORAGE_PATH = 'logs/auth/attempts.json';

    /**
     * @throws Exception
     */
    public function process(Request $request, Closure $next): Response
    {
        // Get client IP address
        $clientIp = $request->getClientIp();

        // Check for basic auth credentials
        $username = $_SERVER['PHP_AUTH_USER'] ?? null;
        $password = $_SERVER['PHP_AUTH_PW'] ?? null;

        // Get expected credentials from environment or config
        $expectedUsername = $this->getConfigUsername();
        $expectedPassword = $this->getConfigPassword();

        // Check if client is allowed to attempt authentication
        // Only check if credentials are provided but incorrect
        if ($username !== null && $password !== null) {
            $authStatus = $this->checkAuthAttemptStatus($clientIp);
            if (!$authStatus['allowed']) {
                $response = new Response('Too many failed attempts. Please try again later.', 429);
                $response->setHeader('Retry-After', (string)$authStatus['wait_time']);
                return $response;
            }
        }

        // Verify credentials
        if (!$username || !$password || $username !== $expectedUsername || $password !== $expectedPassword) {
            // Only record failed attempt if credentials were provided
            if ($username !== null && $password !== null) {
                $this->recordFailedAttempt($clientIp);
            }

            $response = new Response('Authentication required', 401);
            $response->setHeader('WWW-Authenticate', 'Basic realm="Configuration Access ' . time() . '"');
            return $response;
        }

        // Authentication successful - reset failed attempts
        $this->resetFailedAttempts($clientIp);

        return $this->passToNext($request, $next);
    }

    /**
     * Check if client is allowed to attempt authentication
     *
     * @param string $clientIp Client IP address
     * @return array Status array with 'allowed' and 'wait_time' keys
     */
    protected function checkAuthAttemptStatus(string $clientIp): array
    {
        $attempts = $this->getFailedAttempts();

        // If no attempts for this IP, allow
        if (!isset($attempts[$clientIp])) {
            return ['allowed' => true, 'wait_time' => 0];
        }

        $clientAttempts = $attempts[$clientIp];
        $currentTime = time();
        $lastAttemptTime = $clientAttempts['last_attempt'];
        $attemptCount = $clientAttempts['count'];

        // Client is in lockout period after 3 failed attempts
        if ($attemptCount >= self::MAX_ATTEMPTS) {
            $lockoutEndsAt = $lastAttemptTime + self::LOCKOUT_TIME;

            if ($currentTime < $lockoutEndsAt) {
                $waitTime = $lockoutEndsAt - $currentTime;
                return ['allowed' => false, 'wait_time' => $waitTime];
            }

            // Lockout period is over, reset attempts
            $this->resetFailedAttempts($clientIp);
            return ['allowed' => true, 'wait_time' => 0];
        }

        // Check if we need to enforce delay between attempts
        if ($attemptCount > 0) {
            $nextAllowedAttemptTime = $lastAttemptTime + self::ATTEMPT_DELAY;

            if ($currentTime < $nextAllowedAttemptTime) {
                $waitTime = $nextAllowedAttemptTime - $currentTime;
                return ['allowed' => false, 'wait_time' => $waitTime];
            }
        }

        return ['allowed' => true, 'wait_time' => 0];
    }

    /**
     * Record a failed authentication attempt
     *
     * @param string $clientIp Client IP address
     * @return void
     */
    protected function recordFailedAttempt(string $clientIp): void
    {
        $attempts = $this->getFailedAttempts();

        if (!isset($attempts[$clientIp])) {
            $attempts[$clientIp] = [
                'count' => 0,
                'last_attempt' => 0
            ];
        }

        $attempts[$clientIp]['count']++;
        $attempts[$clientIp]['last_attempt'] = time();

        $this->saveFailedAttempts($attempts);
    }

    /**
     * Reset failed attempts counter for an IP
     *
     * @param string $clientIp Client IP address
     * @return void
     */
    protected function resetFailedAttempts(string $clientIp): void
    {
        $attempts = $this->getFailedAttempts();

        if (isset($attempts[$clientIp])) {
            unset($attempts[$clientIp]);
            $this->saveFailedAttempts($attempts);
        }
    }

    /**
     * Get all failed authentication attempts
     *
     * @return array Failed attempts data
     */
    protected function getFailedAttempts(): array
    {
        $storagePath = $this->getStoragePath();

        if (!file_exists($storagePath)) {
            return [];
        }

        $content = file_get_contents($storagePath);
        if (!$content) {
            return [];
        }

        $attempts = json_decode($content, true);
        return is_array($attempts) ? $attempts : [];
    }

    /**
     * Save failed attempts data
     *
     * @param array $attempts Failed attempts data
     * @return void
     */
    protected function saveFailedAttempts(array $attempts): void
    {
        $storagePath = $this->getStoragePath();

        // Ensure directory exists
        $dir = dirname($storagePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($storagePath, json_encode($attempts));
    }

    /**
     * Get storage path for failed attempts
     *
     * @return string Full path to storage file
     */
    protected function getStoragePath(): string
    {
        return implode(DS, [PD, self::STORAGE_PATH]);
    }

    /**
     * Get configured username for authentication
     *
     * @return string Expected username
     */
    protected function getConfigUsername(): string
    {
        // First, try to get from defined constant
        if (defined('CONFIG_USERNAME')) {
            return CONFIG_USERNAME;
        }

        // Then, try from configuration manager
        if (defined('APP_CONFIGURATION')) {
            // Corregido: Ruta correcta para acceder a la configuración
            $configUsername = APP_CONFIGURATION->get('tools.config.username');
            if ($configUsername) {
                return $configUsername;
            }
        }

        // Default fallback
        return 'admin';
    }

    /**
     * Get configured password for authentication
     *
     * @return string Expected password
     */
    protected function getConfigPassword(): string
    {
        // First, try to get from defined constant
        if (defined('CONFIG_PASSWORD')) {
            return CONFIG_PASSWORD;
        }

        // Then, try from configuration manager
        if (defined('APP_CONFIGURATION')) {
            // Corregido: Ruta correcta para acceder a la configuración
            $configPassword = APP_CONFIGURATION->get('tools.config.password');
            if ($configPassword) {
                return $configPassword;
            }
        }

        // Default fallback
        return 'admin';
    }
}
