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

use Catalyst\Framework\Core\Http\Request;
use Catalyst\Helpers\Log\Logger;

// Ensure this file is executed only once
if (defined('INIT_LOGGER_EXECUTED')) {
    return;
}

const INIT_LOGGER_EXECUTED = true;

try {
    // Initialize logger at application start
    $logger = Logger::getInstance();
    $logger->configure([
        'logDirectory' => LOG_DIR,
        'minimumLogLevel' => LOG_LEVEL,
        'displayLogs' => false
    ]);

    // Log application start
    $logger->info('Application started', [
        'environment' => defined('APP_ENV') ? APP_ENV : 'unknown',
        'php_version' => PHP_VERSION,
        'execution_mode' => IS_CLI ? 'CLI' : 'Web',
        'start_time' => RUNTIME_START['TIME'] ?? microtime(true)
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
