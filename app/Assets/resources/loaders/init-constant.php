<?php

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
 * Directory Separator
 *
 * Description: This constant represents the directory separator for the file system paths.
 */
const DS = DIRECTORY_SEPARATOR;

/**
 * Constant for project
 */
require_once realpath(implode(DS, [dirname(__FILE__), '..', 'constant', 'default.php']));
require_once realpath(implode(DS, [dirname(__FILE__), '..', 'constant', 'terminal.php']));
require_once realpath(implode(DS, [dirname(__FILE__), '..', 'constant', 'request.php']));
require_once realpath(implode(DS, [dirname(__FILE__), '..', 'constant', 'environment.php']));
require_once realpath(implode(DS, [dirname(__FILE__), '..', 'constant', 'log.php']));