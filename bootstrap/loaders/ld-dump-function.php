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


use Catalyst\Helpers\Debug\Dumper;

if (!defined('LOADED_DUMP_FUNCTION')) {

    /**
     * Internal function to handle variable dumping
     *
     * @param array $var Variables to dump
     * @param bool $exit Whether to exit after dumping
     * @return void
     */
    function _ex_internal(array $var, bool $exit = false): void
    {
        if (IS_DEVELOPMENT) {
            // Get backtrace information to determine caller
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
            $caller = [
                'file' => $backtrace['file'] ?? 'unknown',
                'line' => $backtrace['line'] ?? 0
            ];

            Dumper::dump(['data' => $var, 'caller' => $caller, 'config' => ['colorTheme' => 'monokai']]);
        } else {
            echo "Dump is disabled in production mode.";
        }

        if ($exit) {
            exit;
        }
    }

    /**
     * Dump variables for inspection
     *
     * @param mixed ...$var Variables to dump
     * @return void
     */
    function ex(...$var): void
    {
        _ex_internal($var);
    }

    /**
     * Dump variables and exit script execution
     *
     * @param mixed ...$var Variables to dump
     * @return never
     */
    function ex_c(...$var): never
    {
        _ex_internal($var, true);
        exit;
    }

    define('LOADED_DUMP_FUNCTION', true);
}