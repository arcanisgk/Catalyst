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

use App\Assets\Helpers\Http\Request;
use App\Assets\Helpers\Log\Logger;


// Para evitar la carga duplicada
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