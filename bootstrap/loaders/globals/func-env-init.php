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

use Catalyst\Helpers\Config\ConfigManager;


if (!defined('APP_CONFIGURATION')) {

    define('APP_CONFIGURATION', ConfigManager::getInstance());
}

if (!function_exists('getConfigurationParameters')) {
    function getConfigurationParameters(string $paramName): array
    {

        return APP_CONFIGURATION->get($paramName);
    }
}


if (!defined('IS_CONFIGURED')) {

    // Define IS_CONFIGURED constant based on configuration status

    define('IS_CONFIGURED', APP_CONFIGURATION->isConfigured());
}
