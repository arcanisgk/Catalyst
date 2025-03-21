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

if (!defined('RUNTIME_START')) {
    /**
     * Defines a constant with runtime performance metrics
     *
     * Captures the current timestamp, memory usage, and peak memory usage
     * at the start of script execution for performance tracking and profiling
     */
    define('RUNTIME_START', [
        'TIME' => microtime(true),
        'MEMORY' => memory_get_usage(),
        'MEMORY_PEAK' => memory_get_peak_usage(),
    ]);
}


$path = implode(DS, array_slice(explode(DS, dirname(__DIR__)), 0, -1));
if (!defined('PD')) {
    /**
     * Project Directory
     *
     * Description: This constant represents the path in which the project is located.
     */
    define('PD', $path);
}

if (!defined('WD')) {
    /**
     * Web Directory
     *
     * Description: This constant represents the path that the web entry point is located on.
     */
    define('WD', trim($_SERVER['DOCUMENT_ROOT'], '/\\') . DS);
}

if (!defined('CT')) {
    /**
     * Current time
     *
     * Description: This constant represents the local server time.
     */
    define('CT', time());
}


#############################################################

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

// Define theme path if not already defined
if (!defined('THEME_PATH')) {
    /**
     * Path to theme-specific views
     */
    define('THEME_PATH', PD . DS . 'bootstrap' . DS . 'template');
}

// Define default layout if not already defined
if (!defined('DEFAULT_LAYOUT')) {
    /**
     * Default layout template name
     */
    define('DEFAULT_LAYOUT', 'default');
}

// Define application name from environment or default
if (!defined('APP_NAME')) {
    /**
     * Application name
     */
    define('APP_NAME', $_ENV['APP_NAME'] ?? 'Catalyst Framework');
}

// Define application version from environment or default
if (!defined('APP_VERSION')) {
    /**
     * Application version
     */
    define('APP_VERSION', $_ENV['APP_VERSION'] ?? '1.0.0');
}

// Define application URL from environment or default
if (!defined('APP_URL')) {
    /**
     * Application base URL
     */
    define('APP_URL', $_ENV['APP_URL'] ?? 'https://catalyst.dock');
}
