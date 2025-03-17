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
 * Set default timezone for the application
 */
date_default_timezone_set('America/Panama');

/**
 * Directory Separator
 *
 * Description: This constant represents the directory separator for the file system paths.
 */
const DS = DIRECTORY_SEPARATOR;

$path = implode(DS, array_slice(explode(DS, dirname(__DIR__)), 0, -3));
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
    define('WD', rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . DS);
}

if (!defined('CT')) {
    /**
     * Current time
     *
     * Description: This constant represents the local server time.
     */
    define('CT', time());
}

