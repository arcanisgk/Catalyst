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

if (!defined('LOG_DIR')) {
    /**
     * Log Directory
     *
     * Description: This constant represents the directory where all logs will be stored.
     */
    define('LOG_DIR', PD . DS . 'logs');
}

if (!defined('LOG_LEVEL')) {
    /**
     * Log Level
     *
     * Description: This constant represents the minimum level of logs to record.
     * It's based on environment - debug level for development, error level for production.
     */
    define('LOG_LEVEL', IS_DEVELOPMENT ? 'DEBUG' : 'ERROR');
}

if (!defined('DISPLAY_LOGS')) {
    /**
     * Display Logs
     *
     * Description: This constant determines whether logs should be displayed in console/browser.
     * By default, logs are NEVER displayed - they are only written to files.
     */
    define('DISPLAY_LOGS', false);
}
