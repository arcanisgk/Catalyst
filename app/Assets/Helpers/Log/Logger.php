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

namespace App\Assets\Helpers\Log;

use App\Assets\Framework\Exceptions\FileSystemException;
use App\Assets\Framework\Traits\SingletonTrait;
use App\Assets\Helpers\ToolBox\DrawBox;
use Exception;

/**
 * Logger class for recording system events, errors, and user activities
 */
class Logger
{
    use SingletonTrait;

    private static bool $hasBeenInitialized = false;

    private bool $logAssetErrors = false; // Default to not logging asset errors

    /**
     * Log levels with their priorities
     */
    private const array LOG_LEVELS = [
        'EMERGENCY' => 0, // System is unusable
        'ALERT' => 1, // Action must be taken immediately
        'CRITICAL' => 2, // Critical conditions
        'ERROR' => 3, // Error conditions
        'WARNING' => 4, // Warning conditions
        'NOTICE' => 5, // Normal but significant condition
        'INFO' => 6, // Informational messages
        'DEBUG' => 7, // Debug-level messages
    ];

    /**
     * Base directory for log files
     */
    private string $logDirectory;

    /**
     * Minimum log level to record
     */
    private int $minimumLogLevel;

    /**
     * Whether to display logs in terminal/browser
     */
    private bool $displayLogs;

    private string $requestId;

    /**
     * Logger constructor.
     */
    protected function __construct()
    {
        // Set default log directory
        $this->logDirectory = PD . DS . 'logs';

        // Create log directory if it doesn't exist
        if (!file_exists($this->logDirectory)) {
            mkdir($this->logDirectory, 0755, true);
        }

        // Set the minimum log level based on environment
        $this->minimumLogLevel = IS_DEVELOPMENT ? self::LOG_LEVELS['DEBUG'] : self::LOG_LEVELS['ERROR'];

        // Default to showing logs in development, hiding in production
        $this->displayLogs = false;

        // Generate a unique request ID
        $this->requestId = uniqid('req-', true);
    }

    /**
     * Configure logger settings - will only run once per request
     *
     * @param array $config Configuration options
     * @return self For method chaining
     */
    public function configure(array $config): self
    {
        // Options that can be reconfigured at any time
        $this->configureRuntimeOptions($config);

        // Options that can only be set once during initialization
        if (!self::$hasBeenInitialized) {
            $this->configureInitialOptions($config);
            self::$hasBeenInitialized = true;
        }

        return $this;
    }

    /**
     * Configure options that can be changed at runtime
     *
     * @param array $config Configuration options
     * @return void
     */
    private function configureRuntimeOptions(array $config): void
    {
        // These options can be changed even after initialization
        if (isset($config['logAssetErrors'])) {
            $this->logAssetErrors = (bool)$config['logAssetErrors'];
        }

        // Add other runtime-configurable options here
    }

    /**
     * Configure options that can only be set during initialization
     *
     * @param array $config Configuration options
     * @return void
     */
    private function configureInitialOptions(array $config): void
    {
        // These options can only be set once during initialization
        if (isset($config['logDirectory'])) {
            $this->logDirectory = $config['logDirectory'];

            if (!file_exists($this->logDirectory)) {
                mkdir($this->logDirectory, 0755, true);
            }
        }

        if (isset($config['minimumLogLevel']) && array_key_exists($config['minimumLogLevel'], self::LOG_LEVELS)) {
            $this->minimumLogLevel = self::LOG_LEVELS[$config['minimumLogLevel']];
        }

        if (isset($config['displayLogs'])) {
            $this->displayLogs = (bool)$config['displayLogs'];
        }
    }

    /**
     * Log a message with a specific level
     *
     * @param string $level Log level
     * @param string $message Log message
     * @param array $context Additional context data
     * @return void Success status
     * @throws Exception
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $level = strtoupper($level);

        // Check if this level should be logged
        if (!array_key_exists($level, self::LOG_LEVELS) || self::LOG_LEVELS[$level] > $this->minimumLogLevel) {
            return;
        }

        // For web requests, automatically downgrade log level for asset requests
        // Apply for all logs
        if (!IS_CLI) {
            $requestType = $this->classifyRequest();

            // For asset requests, filter based on configuration and level
            if ($requestType === 'asset') {
                if ($level === 'ERROR' && !$this->logAssetErrors) {
                    return;
                }
                // Keep existing logic for INFO levels
                if ($level === 'INFO' && self::LOG_LEVELS['DEBUG'] > $this->minimumLogLevel) {
                    return;
                }
            }
        }

        // Original logging code continues...
        $logEntry = $this->formatLogEntry($level, $message, $context);
        $logFile = $this->getLogFilePath($level);

        $this->writeToLogFile($logFile, $logEntry);
    }

    /**
     * Format a log entry
     *
     * @param string $level Log level
     * @param string $message Log message
     * @param array $context Additional context data
     * @return string Formatted log entry
     */
    private function formatLogEntry(string $level, string $message, array $context = []): string
    {
        $timestamp = date('Y-m-d H:i:s');
        $userId = $this->getCurrentUserId();
        $ipAddress = $this->getClientIp();

        // Add request metadata to all logs if it's not CLI
        if (!IS_CLI && !isset($context['request_metadata'])) {
            $context['request_metadata'] = [
                'uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
                'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
                'referer' => $_SERVER['HTTP_REFERER'] ?? 'direct',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'request_id' => $this->getRequestId()
            ];
        }

        // Format: [Timestamp] [Level] [IP] [User:ID] Message Context
        $logEntry = sprintf(
            "[%s] [%s] [%s] [User:%s] %s",
            $timestamp,
            $level,
            $ipAddress,
            $userId,
            $message
        );

        // Add context as JSON
        if (!empty($context)) {
            $logEntry .= " " . json_encode($context);
        }

        return $logEntry;
    }

    /**
     * Get a unique ID for this request
     *
     * @return string Request ID
     */
    private function getRequestId(): string
    {
        static $requestId = null;

        if ($requestId === null) {
            $requestId = uniqid('', true);
        }

        return $requestId;
    }

    /**
     * Classify the current request type
     *
     * @return string Request classification
     */
    private function classifyRequest(): string
    {
        if (IS_CLI) {
            return 'cli';
        }

        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // Check for API requests
        if (str_starts_with($uri, '/api/') || str_contains($accept, 'application/json')) {
            return 'api';
        }

        // Check for asset requests (JS, CSS, images, etc.)
        if (preg_match('/\.(js|css|jpg|jpeg|png|gif|svg|woff|woff2|ttf|ico)$/i', $uri)) {
            return 'asset';
        }

        // Check for bot/crawler
        if (preg_match('/(bot|crawler|spider|slurp|yahoo|bingbot|googlebot)/i', $userAgent)) {
            return 'bot';
        }

        // Check for AJAX request
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return 'ajax';
        }

        // Default for normal page views
        return 'page';
    }


    /**
     * Get the log file path for a specific level
     *
     * @param string $level Log level
     * @return string Full path to log file
     */
    private function getLogFilePath(string $level): string
    {
        // Clasificar logs por tipo
        $category = match (true) {
            in_array($level, ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR']) => 'errors',
            in_array($level, ['WARNING', 'NOTICE']) => 'events',
            default => 'info'
        };

        // Crear directorio de categoría si no existe
        $categoryDir = $this->logDirectory . DS . $category;
        if (!file_exists($categoryDir)) {
            mkdir($categoryDir, 0755, true);
        }

        // Usar rotación diaria de logs
        $filename = date('Y-m-d') . '.log';
        return $categoryDir . DS . $filename;
    }

    /**
     * Write entry to log file
     *
     * @param string $logFile Log file path
     * @param string $logEntry Formatted log entry
     * @return void
     * @throws Exception
     */
    private function writeToLogFile(string $logFile, string $logEntry): void
    {
        try {
            $result = file_put_contents(
                $logFile,
                $logEntry . PHP_EOL,
                FILE_APPEND | LOCK_EX
            );

            if ($result === false) {
                throw FileSystemException::unableToWriteFile($logFile);
            }
        } catch (Exception $e) {
            // Log to error_log as a last resort
            error_log("Failed to write to log file '$logFile': " . $e->getMessage());

            if (IS_DEVELOPMENT) {
                throw $e; // Re-throw in development
            }
            // In production, fail silently but ensure error is logged somewhere
        }
    }

    /**
     * Display log in terminal or browser - will only be used if explicitly enabled
     *
     * @param string $level Log level
     * @param string $logEntry Formatted log entry
     * @return void
     */
    private function displayLog(string $level, string $logEntry): void
    {
        // Don't display logs unless explicitly configured to do so
        if (!$this->displayLogs) {
            return;
        }

        $style = match ($level) {
            'EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR' => 2, // Error style (red)
            'WARNING' => 3, // Warning style (yellow)
            'NOTICE' => 4, // Info style (blue)
            'INFO' => 7, // Info alt style (cyan)
            'DEBUG' => 0, // Default style
            default => 0, // Default style
        };

        $drawBox = DrawBox::getInstance();
        echo $drawBox->draw($logEntry, [
            'headerLines' => 0,
            'footerLines' => 0,
            'highlight' => true,
            'maxWidth' => 0,
            'style' => $style,
            'isError' => $level === 'ERROR'
        ]);
    }


    /**
     * Get the current user ID
     *
     * @return string User ID or 'guest' if not logged in
     */
    private function getCurrentUserId(): string
    {
        // This would need to be adapted to your authentication system
        // For now, a placeholder implementation
        return $_SESSION['user_id'] ?? 'guest';
    }

    /**
     * Get client IP address
     *
     * @return string IP address
     */
    private function getClientIp(): string
    {
        if (IS_CLI) {
            return 'CLI';
        }

        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    /**
     * Log an emergency message
     *
     * @param string $message Message to log
     * @param array $context Additional context
     * @return void Success status
     * @throws Exception
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->log('EMERGENCY', $message, $context);
    }

    /**
     * Log an alert message
     *
     * @param string $message Message to log
     * @param array $context Additional context
     * @return void Success status
     * @throws Exception
     */
    public function alert(string $message, array $context = []): void
    {
        $this->log('ALERT', $message, $context);
    }

    /**
     * Log a critical message
     *
     * @param string $message Message to log
     * @param array $context Additional context
     * @return void Success status
     * @throws Exception
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log('CRITICAL', $message, $context);
    }

    /**
     * Log an error message
     *
     * @param string $message Message to log
     * @param array $context Additional context
     * @return void Success status
     * @throws Exception
     */
    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    /**
     * Log a warning message
     *
     * @param string $message Message to log
     * @param array $context Additional context
     * @return void Success status
     * @throws Exception
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    /**
     * Log a notice message
     *
     * @param string $message Message to log
     * @param array $context Additional context
     * @return void Success status
     * @throws Exception
     */
    public function notice(string $message, array $context = []): void
    {
        $this->log('NOTICE', $message, $context);
    }

    /**
     * Log an info message
     *
     * @param string $message Message to log
     * @param array $context Additional context
     * @return void Success status
     * @throws Exception
     */
    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    /**
     * Log a debug message
     *
     * @param string $message Message to log
     * @param array $context Additional context
     * @return void Success status
     * @throws Exception
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }

    /**
     * Log a system event
     *
     * @param string $event Event name
     * @param string $message Event description
     * @param array $context Additional context
     * @return void Success status
     * @throws Exception
     */
    public function system(string $event, string $message, array $context = []): void
    {
        $context['event_type'] = 'system';
        $context['event_name'] = $event;
        $this->info($message, $context);
    }

    /**
     * Log an email event
     *
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param array $context Additional context
     * @return bool Success status
     */
    public function email(string $to, string $subject, array $context = []): bool
    {
        $message = "Email sent to: $to, Subject: $subject";

        // Guardar en directorio específico de emails
        $emailDir = $this->logDirectory . DS . 'email';
        if (!file_exists($emailDir)) {
            mkdir($emailDir, 0755, true);
        }

        $logFile = $emailDir . DS . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $userId = $this->getCurrentUserId();
        $ipAddress = $this->getClientIp();

        $logEntry = sprintf(
            "[%s] [EMAIL] [%s] [User:%s] %s",
            $timestamp,
            $ipAddress,
            $userId,
            $message
        );

        if (!empty($context)) {
            $logEntry .= " " . json_encode($context);
        }

        return (bool)file_put_contents(
            $logFile,
            $logEntry . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }

    /**
     * Log a user event
     *
     * @param string $event Event name
     * @param string $message Event description
     * @param array $context Additional context
     * @return void Success status
     * @throws Exception
     */
    public function user(string $event, string $message, array $context = []): void
    {
        $context['event_type'] = 'user';
        $context['event_name'] = $event;
        $this->info($message, $context);
    }
}
