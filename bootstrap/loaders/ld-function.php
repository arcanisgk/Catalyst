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

/**
 * Format execution time in a human-readable format
 *
 * @param float $startTime Start time from microtime(true)
 * @return string Formatted execution time
 */
function format_execution_time(float $startTime): string
{
    $duration = microtime(true) - $startTime;

    if ($duration < 0.001) {
        return round($duration * 1000000) . 'μs'; // microseconds
    } elseif ($duration < 1) {
        return round($duration * 1000, 2) . 'ms'; // milliseconds
    } else {
        return round($duration, 4) . 's'; // seconds
    }
}

/**
 * Format memory usage in a human-readable format
 *
 * @param int $bytes Memory usage in bytes
 * @return string Formatted memory usage
 */
function format_memory(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, 2) . ' ' . $units[$pow];
}