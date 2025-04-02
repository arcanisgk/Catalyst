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
 *
 */


if (IS_DEVELOPMENT && !IS_CLI) {
    // Development environment settings
    ini_set('opcache.enable', '0');
    ini_set('opcache.enable_cli', '0');
    ini_set('opcache.revalidate_freq', '0');
    ini_set('opcache.validate_timestamps', '1');
    ini_set('opcache.save_comments', '1');
    ini_set('realpath_cache_size', '0');
    ini_set('realpath_cache_ttl', '0');
    ini_set('apc.enabled', '0');
    ini_set('apc.enable_cli', '0');
    ini_set('session.cache_limiter', 'nocache');

    // Response headers for preventing browser caching
    header_remove('Pragma');
    header_remove('Cache-Control');
    header_remove('Expires');
    header('Pragma: no-cache');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Expires: 0');
}