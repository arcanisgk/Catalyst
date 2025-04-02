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
 * CsrfProtection component for the Catalyst Framework
 *
 */

namespace Catalyst\Helpers\Security;

use Catalyst\Framework\Traits\SingletonTrait;
use Catalyst\Helpers\Log\Logger;
use Exception;
use Random\RandomException;

/**
 * CSRF Protection Helper
 *
 * Handles CSRF token generation, storage and validation
 */
class CsrfProtection
{
    use SingletonTrait;

    /**
     * Session key for storing CSRF tokens
     */
    private const string SESSION_KEY = 'catalyst_csrf_tokens';

    /**
     * Default token expiration time (30 minutes)
     */
    private const int DEFAULT_EXPIRY = 1800; // 30 minutes

    /**
     * Maximum number of tokens to store
     */
    private const int MAX_TOKENS = 10;

    protected ?Logger $logger = null;

    public function __construct()
    {
        $this->logger = Logger::getInstance();
    }

    /**
     * Generate a new CSRF token
     *
     * @param string|null $action Optional action context for the token
     * @param int|null $expiry Token expiration time in seconds
     * @return string The generated token
     * @throws RandomException
     * @throws Exception
     */
    public function generateToken(?string $action = null, ?int $expiry = null): string
    {
        // Start session if not already started
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Initialize tokens array if it doesn't exist
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_array($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }

        // Clean expired tokens
        $this->cleanExpiredTokens();

        // Generate a cryptographically secure random token
        $token = bin2hex(random_bytes(32));

        // Store token with metadata
        $_SESSION[self::SESSION_KEY][$token] = [
            'action' => $action,
            'expires' => time() + ($expiry ?? self::DEFAULT_EXPIRY)
        ];

        if (defined('IS_DEVELOPMENT') && IS_DEVELOPMENT && $this->logger) {
            $this->logger->debug('CSRF token generated', [
                'token' => $token,
                'session_id' => session_id(),
                'session_status' => session_status()
            ]);
        }

        // Limit number of stored tokens
        if (count($_SESSION[self::SESSION_KEY]) > self::MAX_TOKENS) {
            // Remove oldest token
            reset($_SESSION[self::SESSION_KEY]);
            $oldestToken = key($_SESSION[self::SESSION_KEY]);
            unset($_SESSION[self::SESSION_KEY][$oldestToken]);
        }

        return $token;
    }

    /**
     * Validate a CSRF token
     *
     * @param string $token Token to validate
     * @param string|null $action Optional action context for validation
     * @param bool $removeOnSuccess Whether to remove the token after successful validation
     * @return bool Whether the token is valid
     * @throws Exception
     */
    public function validateToken(string $token, ?string $action = null, bool $removeOnSuccess = false): bool
    {
        // Start session if not already started
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (defined('IS_DEVELOPMENT') && IS_DEVELOPMENT && $this->logger) {
            $this->logger->debug('CSRF validation track', [
                'token_received_to_validate' => $token,
                'session_values' => $_SESSION[self::SESSION_KEY] ?? []
            ]);
        }

        // If no tokens exist, validation fails
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_array($_SESSION[self::SESSION_KEY])) {
            return false;
        }

        // Check if token exists
        if (!isset($_SESSION[self::SESSION_KEY][$token])) {
            return false;
        }

        $tokenData = $_SESSION[self::SESSION_KEY][$token];

        // Check if token has expired
        if ($tokenData['expires'] < time()) {
            // Remove expired token
            unset($_SESSION[self::SESSION_KEY][$token]);
            return false;
        }

        // Check action if specified
        if ($action !== null && $tokenData['action'] !== $action) {
            return false;
        }

        // Token is valid, remove if required
        if ($removeOnSuccess) {
            unset($_SESSION[self::SESSION_KEY][$token]);
        }

        return true;
    }

    /**
     * Clean expired tokens from session
     *
     * @return void
     */
    private function cleanExpiredTokens(): void
    {
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_array($_SESSION[self::SESSION_KEY])) {
            return;
        }

        $now = time();
        foreach ($_SESSION[self::SESSION_KEY] as $token => $data) {
            if ($data['expires'] < $now) {
                unset($_SESSION[self::SESSION_KEY][$token]);
            }
        }
    }

    /**
     * Get HTML input field for CSRF token
     *
     * @param string|null $action Optional action context for the token
     * @return string HTML input field
     * @throws RandomException
     */
    public function getTokenField(?string $action = null): string
    {
        $token = $this->generateToken($action);
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}
