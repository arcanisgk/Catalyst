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


if (!defined('LOADED_COMMONS_FUNCTION')) {

    /**
     * Format execution time in a human-readable format
     *
     * @param float $startTime Start time from microtime(true)
     * @return string Formatted execution time
     */
    function format_execution_time(float $startTime): string
    {
        $duration = microtime(true) - $startTime;

        if ($duration < 0.001) {
            return round($duration * 1000000) . 'μs'; // microseconds
        } elseif ($duration < 1) {
            return round($duration * 1000, 2) . 'ms'; // milliseconds
        } else {
            return round($duration, 4) . 's'; // seconds
        }
    }

    /**
     * Format memory usage in a human-readable format
     *
     * @param int $bytes Memory usage in bytes
     * @return string Formatted memory usage
     */
    function format_memory(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    define('LOADED_COMMONS_FUNCTION', true);
}