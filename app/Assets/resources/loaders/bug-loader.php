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

require_once __DIR__ . '/../loaders/init-constant.php';
require_once PD . '/vendor/autoload.php';
require_once PD . '/app/Assets/Helpers/Error/BugCatcher.php';

if (!defined('BUG_CATCHER_LOADED')) {
    define('BUG_CATCHER_LOADED', true);
}


