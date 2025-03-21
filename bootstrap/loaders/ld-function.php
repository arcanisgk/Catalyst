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

use Catalyst\Assets\Framework\Core\Http\Request;
use Catalyst\Helpers\Debug\Dumper;
use Catalyst\Helpers\Log\Logger;

/**
 * Helper function for logging messages
 *
 * @param string $level Log level (emergency, alert, critical, error, warning, notice, info, debug)
 * @param string $message Message to log
 * @param array $context Additional context information
 * @return void Success status
 * @throws Exception
 */
function log_message(string $level, string $message, array $context = []): void
{
    Logger::getInstance()->log($level, $message, $context);
}

/**
 * Helper function for logging errors
 *
 * @param string $message Error message
 * @param array $context Additional context information
 * @return void Success status
 * @throws Exception
 */
function log_error(string $message, array $context = []): void
{
    Logger::getInstance()->error($message, $context);
}

/**
 * Helper function for logging debug information
 *
 * @param string $message Debug message
 * @param array $context Additional context information
 * @return void Success status
 * @throws Exception
 */
function log_debug(string $message, array $context = []): void
{
    Logger::getInstance()->debug($message, $context);
}

/**
 * Helper function for logging system events
 *
 * @param string $event Event name
 * @param string $message Event description
 * @param array $context Additional context information
 * @return void Success status
 * @throws Exception
 */
function log_system(string $event, string $message, array $context = []): void
{
    Logger::getInstance()->system($event, $message, $context);
}

/**
 * Helper function for logging user events
 *
 * @param string $event Event name
 * @param string $message Event description
 * @param array $context Additional context information
 * @return void Success status
 * @throws Exception
 */
function log_user(string $event, string $message, array $context = []): void
{
    Logger::getInstance()->user($event, $message, $context);
}

/**
 * Dump variables for inspection
 *
 * @param mixed ...$var Variables to dump
 * @return void
 */
function ex(...$var): void
{
    if (IS_DEVELOPMENT) {
        // Get backtrace information to determine caller
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
        $caller = [
            'file' => $backtrace['file'] ?? 'unknown',
            'line' => $backtrace['line'] ?? 0
        ];

        Dumper::dump(['data' => $var, 'caller' => $caller]);
    } else {
        echo "Dump is disabled in production mode.";
    }
}

/**
 * Dump variables and exit script execution
 *
 * @param mixed ...$var Variables to dump
 * @return never
 */
function ex_c(...$var): never
{
    if (IS_DEVELOPMENT) {
        // Get backtrace information to determine caller
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
        $caller = [
            'file' => $backtrace['file'] ?? 'unknown',
            'line' => $backtrace['line'] ?? 0
        ];

        Dumper::dump(['data' => $var, 'caller' => $caller]);
    } else {
        echo "Dump is disabled in production mode.";
    }
    exit;
}

if (defined('INIT_LOADER_EXECUTED')) {
    return;
} else {
    define('INIT_LOADER_EXECUTED', true);

    try {
        // Inicializar el logger al inicio de la aplicación
        $logger = Logger::getInstance();
        $logger->configure([
            'logDirectory' => LOG_DIR,
            'minimumLogLevel' => LOG_LEVEL,
            'displayLogs' => false
        ]);

        // Registrar inicio de aplicación

        $logger->info('Application started', [
            'environment' => defined('APP_ENV') ? APP_ENV : 'unknown',
            'php_version' => PHP_VERSION,
            'execution_mode' => IS_CLI ? 'CLI' : 'Web'
        ]);

        // Initialize the Request handler for web requests
        if (!IS_CLI) {
            Request::getInstance();
            $logger->debug('Request handler initialized');
        }

    } catch (Exception $e) {
        // Fallback error handling if logger fails
        error_log('Logger initialization failed: ' . $e->getMessage());

        // Only display error in development mode
        if (IS_DEVELOPMENT) {
            echo 'Logger initialization error: ' . $e->getMessage();
        }
    }
}