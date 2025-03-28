<?php

declare(strict_types=1);

/**************************************************************************************
 *
 * Catalyst PHP Framework
 * PHP Version 8.3 (Required).
 *
 * @see https://github.com/arcanisgk/catalyst
 *
 * @author    Walter Nuñez (arcanisgk/original founder) <icarosnet@gmail.com>
 * @copyright 2023 - 2024
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.
 *
 */

namespace Catalyst\Framework\Core\Session;

use Catalyst\Framework\Traits\SingletonTrait;
use Catalyst\Helpers\Log\Logger;
use Exception;
use RuntimeException;

/**
 * SessionManager class for managing application sessions
 *
 * Handles session initialization, data storage/retrieval, and flash messages.
 *
 * @package Catalyst\Framework\Core\Session
 */
class SessionManager
{
    use SingletonTrait;

    /**
     * Whether the session has been initialized
     *
     * @var bool
     */
    protected bool $initialized = false;

    /**
     * Session configuration
     *
     * @var array
     */
    protected array $config = [
        'name' => 'catalyst-session',
        'lifetime' => 2592000,        // 30 days
        'activity_timeout' => 172800, // 2 days
        'use_activity_timeout' => true,
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Strict'
    ];

    /**
     * Get the session configuration
     *
     * @return array Session configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Set the session configuration
     *
     * @param array $config Configuration options
     * @return self For method chaining
     */
    public function setConfig(array $config): self
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }

    /**
     * Initialize the session with provided configuration
     *
     * @param array $config Configuration options
     * @return self For method chaining
     * @throws RuntimeException|Exception If session cannot be started
     */
    public function init(array $config = []): self
    {
        if ($this->initialized) {
            return $this;
        }

        // If no explicit config provided, load from configuration system
        if (empty($config) && defined('APP_CONFIGURATION')) {
            $sessionConfig = APP_CONFIGURATION->get('session.session', []);

            // Map session.json keys to SessionManager config keys
            if (!empty($sessionConfig)) {
                $config = [
                    'name' => $sessionConfig['session_name'] ?? $this->config['name'],
                    'lifetime' => $sessionConfig['session_life_time'] ?? $this->config['lifetime'],
                    'activity_timeout' => $sessionConfig['session_activity_expire'] ?? $this->config['activity_timeout'],
                    'use_activity_timeout' => $sessionConfig['session_inactivity'] ?? $this->config['use_activity_timeout'],
                    'secure' => $sessionConfig['session_secure'] ?? $this->config['secure'],
                    'httponly' => $sessionConfig['session_http_only'] ?? $this->config['httponly'],
                    'samesite' => $sessionConfig['session_same_site'] ?? $this->config['samesite']
                ];
            }
        }

        if (!empty($config)) {
            $this->setConfig($config);
        }

        // Configure session cookie parameters
        session_set_cookie_params([
            'lifetime' => $this->config['lifetime'],
            'path' => '/',
            'domain' => '',
            'secure' => $this->config['secure'],
            'httponly' => $this->config['httponly'],
            'samesite' => $this->config['samesite']
        ]);

        // Set session name
        session_name($this->config['name']);

        // Start the session
        if (session_status() === PHP_SESSION_NONE) {
            if (!session_start()) {
                throw new RuntimeException('Failed to start session');
            }
        }

        // Check activity timeout if enabled
        if ($this->config['use_activity_timeout'] && isset($_SESSION['_last_activity'])) {
            if (time() - $_SESSION['_last_activity'] > $this->config['activity_timeout']) {
                $this->destroy();
                session_start();
            }
        }

        // Update last activity time
        $_SESSION['_last_activity'] = time();

        $this->initialized = true;

        // Log session initialization if in development mode
        if (defined('IS_DEVELOPMENT') && IS_DEVELOPMENT && class_exists('\Catalyst\Helpers\Log\Logger')) {
            Logger::getInstance()->debug('Session initialized', [
                'name' => $this->config['name'],
                'lifetime' => $this->config['lifetime'],
                'use_activity_timeout' => $this->config['use_activity_timeout']
            ]);
        }

        return $this;
    }

    /**
     * Check if a session variable exists
     *
     * @param string $key Session variable key
     * @return bool True if the variable exists
     */
    public function has(string $key): bool
    {
        $this->ensureInitialized();
        return isset($_SESSION[$key]);
    }

    /**
     * Get a session variable
     *
     * @param string $key Session variable key
     * @param mixed $default Default value if not found
     * @return mixed Session variable value or default
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $this->ensureInitialized();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Set a session variable
     *
     * @param string $key Session variable key
     * @param mixed $value Session variable value
     * @return self For method chaining
     */
    public function set(string $key, mixed $value): self
    {
        $this->ensureInitialized();
        $_SESSION[$key] = $value;
        return $this;
    }

    /**
     * Remove a session variable
     *
     * @param string $key Session variable key
     * @return self For method chaining
     */
    public function remove(string $key): self
    {
        $this->ensureInitialized();
        unset($_SESSION[$key]);
        return $this;
    }

    /**
     * Get all session data
     *
     * @return array All session data
     */
    public function all(): array
    {
        $this->ensureInitialized();
        return $_SESSION;
    }

    /**
     * Clear all session data
     *
     * @return self For method chaining
     */
    public function clear(): self
    {
        $this->ensureInitialized();
        $_SESSION = [];
        return $this;
    }

    /**
     * Destroy the current session
     *
     * @return self For method chaining
     */
    public function destroy(): self
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            // Clear session data
            $_SESSION = [];

            // Delete the session cookie
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    [
                        'expires' => time() - 42000,
                        'path' => $params['path'],
                        'domain' => $params['domain'],
                        'secure' => $params['secure'],
                        'httponly' => $params['httponly'],
                        'samesite' => $params['samesite'] ?? 'Lax'
                    ]
                );
            }

            // Destroy the session
            session_destroy();
        }

        $this->initialized = false;
        return $this;
    }

    /**
     * Regenerate the session ID
     *
     * @param bool $deleteOldSession Whether to delete the old session data
     * @return self For method chaining
     */
    public function regenerateId(bool $deleteOldSession = true): self
    {
        $this->ensureInitialized();
        session_regenerate_id($deleteOldSession);
        return $this;
    }

    /**
     * Check if the session is initialized
     *
     * @return bool True if the session is initialized
     */
    public function isInitialized(): bool
    {
        return $this->initialized;
    }

    /**
     * Ensure the session is initialized
     *
     * @return void
     * @throws RuntimeException If session is not initialized
     */
    protected function ensureInitialized(): void
    {
        if (!$this->initialized) {
            throw new RuntimeException('Session not initialized. Call init() first.');
        }
    }
}