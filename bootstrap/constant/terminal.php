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

if (!defined('IS_CLI')) {
    $isCLI = defined('STDIN')
        || php_sapi_name() === 'cli'
        || (stristr(PHP_SAPI, 'cgi') && getenv('TERM'))
        || (empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0);

    /**
     * Determines if the current PHP script is running in CLI (Command Line Interface) mode.
     *
     * Checks multiple conditions to detect CLI environment, including:
     * - Presence of STDIN
     * - PHP sapi name is 'cli'
     * - CGI mode with terminal environment
     * - Absence of web server context and presence of command line arguments
     *
     * Defines a constant 'IS_CLI' with the result of the detection.
     */
    define('IS_CLI', $isCLI);
}


if (!defined('TW') && IS_CLI) {
    /**
     * Terminal Width
     *
     * Description: This constant represents the local server time.
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

    /**
     * Determines and defines the terminal width constant (TW) for CLI environments.
     *
     * Detects terminal width across different operating systems:
     * - On Windows, uses 'mode con' command to retrieve column width
     * - On other systems, uses 'tput cols' command
     * - Defaults to 80 columns if width cannot be determined
     *
     * The constant is only defined if not already set and the script is running in CLI mode.
     */
    define('TW', $termWidth);
}


if (!defined('NL')) {

    $nl = defined('STDIN')
        || php_sapi_name() === "cli"
        || (stristr(PHP_SAPI, 'cgi') && getenv('TERM'))
        || (empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0);

    /**
     *
     * New Line
     *
     * Description: This constant represents a new line for system on request or command execution.
     */
    define('NL', $nl ? PHP_EOL : trim(nl2br(PHP_EOL)));
}