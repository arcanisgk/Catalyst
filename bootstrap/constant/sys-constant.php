<?php

declare(strict_types=1);

/**************************************************************************************
 *
 * Catalyst PHP Framework
 * PHP Version 8.3 (Required).
 *
 * @package   Catalyst
 * @subpackage Public
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
 */

/**
 * Check PHP version requirement
 */
if (!version_compare(phpversion(), '8.3', '>=')) {
    die("This project requires PHP version 8.3 or higher");
}

/**
 * Set default timezone for the application
 */
date_default_timezone_set('America/Panama');


if (!defined('LOADED_SYS_CONSTANT')) {


    // Runtime start constants
    if (!defined('RUNTIME_START')) {
        /**
         * Defines a constant with runtime performance metrics
         */
        define('RUNTIME_START', [
            'TIME' => microtime(true),
            'MEMORY' => memory_get_usage(),
            'MEMORY_PEAK' => memory_get_peak_usage(),
        ]);
    }

    if (!defined('DS')) {
        define('DS', DIRECTORY_SEPARATOR);
    }


    // Path constants
    $path = implode(DS, array_slice(explode(DS, dirname(__DIR__)), 0, -1));
    if (!defined('PD')) {
        /**
         * Project Directory
         */
        define('PD', $path);
    }

    if (!defined('WD') && isset($_SERVER['DOCUMENT_ROOT'])) {
        /**
         * Web Directory
         */
        define('WD', trim($_SERVER['DOCUMENT_ROOT'], '/\\') . DS);
    }

    // Time constants
    if (!defined('CT')) {
        /**
         * Current time
         */
        define('CT', time());
    }

    // CLI detection
    if (!defined('IS_CLI')) {
        $isCLI = defined('STDIN')
            || php_sapi_name() === 'cli'
            || (stristr(PHP_SAPI, 'cgi') && getenv('TERM'))
            || (empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0);

        /**
         * Determines if the current PHP script is running in CLI mode
         */
        define('IS_CLI', $isCLI);
    }

    // Terminal constants
    if (!defined('TW') && IS_CLI) {
        /**
         * Terminal Width
         */
        $termWidth = null;

        if (str_contains(PHP_OS, 'WIN')) {
            $termWidth = shell_exec('mode con');
            preg_match('/CON.*:(\n[^|]+?){3}(?<cols>\d+)/', $termWidth, $match);
            $termWidth = isset($match['cols']) ? (int)$match['cols'] : null;
        } elseif (function_exists('shell_exec')) {
            $termResponse = shell_exec('tput cols 2> /dev/tty');
            if ($termResponse !== null) {
                $termWidth = trim($termResponse) ?? null;
                if ($termWidth !== null) {
                    $termWidth = (int)$termWidth;
                }
            }
        }

        if ($termWidth === null) {
            $termWidth = 80;
        }

        define('TW', $termWidth);
    }

    if (!defined('NL')) {
        $nl = defined('STDIN')
            || php_sapi_name() === "cli"
            || (stristr(PHP_SAPI, 'cgi') && getenv('TERM'))
            || (empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0);

        /**
         * New Line
         */
        define('NL', $nl ? PHP_EOL : trim(nl2br(PHP_EOL)));
    }

    // Request constants
    if (!defined('RQ') && isset($_SERVER['REQUEST_METHOD'])) {
        /**
         * HTTP Request Method
         */
        define('RQ', $_SERVER['REQUEST_METHOD']);
    }

    if (!defined('UR') && isset($_SERVER['REQUEST_URI'])) {
        /**
         * HTTP Request URI
         */
        define('UR', $_SERVER['REQUEST_URI']);
    }

    // Theme/Application constants
    if (!defined('THEME_PATH')) {
        /**
         * Path to theme-specific views
         */
        define('THEME_PATH', implode(DS, [PD, 'bootstrap', 'template']));
    }

    if (!defined('DEFAULT_LAYOUT')) {
        /**
         * Default layout template name
         */
        define('DEFAULT_LAYOUT', 'default');
    }

    // Log constants
    if (!defined('LOG_DIR')) {
        /**
         * Log Directory
         */
        define('LOG_DIR', implode(DS, [PD, 'logs']));
    }

    if (!defined('DISPLAY_LOGS')) {
        /**
         * Display Logs setting
         */
        define('DISPLAY_LOGS', false);
    }

    define('LOADED_SYS_CONSTANT', true);
}