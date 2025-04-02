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
 * ld-bug-catcher.php
 *
 * This file serves as the primary entry point for the error handling system in the Catalyst framework.
 * It loads only the necessary components for error capturing and logging without initializing
 * the entire framework environment. This allows for early error detection before the main
 * application code executes.
 *
 * The script follows these steps:
 * 1. Load essential constants
 * 2. Define a minimal manual autoloader for error handling classes
 * 3. Initialize the BugCatcher system
 *
 * A flag (BUG_CATCHER_LOADED) is set to prevent duplicate initialization when the
 * Composer autoloader later loads the same classes.
 */

require_once realpath(implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), '..', 'constant', 'sys-constant.php']));
require_once implode(DS, [PD, 'bootstrap', 'constant', 'class-constant.php']);

if (!defined('INITIALIZED_BUG_CATCHER')) {
    spl_autoload_register(function ($class) {
        $supportedNamespaces = [
            'Catalyst\\Helpers\\Error\\' => 'app/Assets/Helpers/Error',
            'Catalyst\\Framework\\Traits\\' => 'app/Assets/Framework/Traits',
            'Catalyst\\Helpers\\Log\\' => 'app/Assets/Helpers/Log',
            'Catalyst\\Helpers\\ToolBox\\' => 'app/Assets/Helpers/ToolBox',
            'Catalyst\\Helpers\\IO\\' => 'app/Assets/Helpers/IO',
            'Catalyst\\Framework\\Core\\Exceptions\\' => 'app/Framework/Core/Exceptions',
            'Catalyst\\Framework\\Core\\Argument\\' => 'app/Framework/Core/Argument'
        ];

        foreach ($supportedNamespaces as $namespace => $path) {
            if (str_starts_with($class, $namespace)) {
                $relativeClass = substr($class, strlen($namespace));

                $file = implode(DS, [PD, str_replace('\\', DS, $path), str_replace('\\', DS, $relativeClass) . '.php']);

                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
        }
        return false;
    });

    $bugCatcherPath = implode(DS, [PD, 'app', 'Assets', 'Helpers', 'Error', 'BugCatcher.php']);
    if (file_exists($bugCatcherPath)) {
        require_once $bugCatcherPath;

        if (!defined('INITIALIZED_BUG_CATCHER')) {
            define('INITIALIZED_BUG_CATCHER', true);
        }
    }
}

