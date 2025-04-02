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


// Get the base directory of the project
$baseDir = dirname(__FILE__, 2);

// Build paths in a platform-independent way
$phpIniPath = "C:/laragon/bin/php/php-8.3.3-nts-Win32-vs16-x64/php.ini";
$indexPath = implode(DIRECTORY_SEPARATOR, [$baseDir, 'bootstrap', 'bin', 'index.php']);

// Get PHP executable path
$phpExecutable = PHP_BINARY;

// Capture all command line arguments to forward them
$arguments = '';
if (isset($argv) && count($argv) > 1) {
    // Skip the first argument (script name)
    $args = array_slice($argv, 1);
    // Escape each argument and join them
    $escapedArgs = array_map('escapeshellarg', $args);
    $arguments = ' ' . implode(' ', $escapedArgs);
}

// Build the command
$command = escapeshellcmd("$phpExecutable -c \"$phpIniPath\" \"$indexPath\"") . $arguments;

// Output what we're doing
echo "Executing: $command\n";

// Execute the command and pass through all output
passthru($command, $returnCode);

// Return the same exit code from the subprocess
exit($returnCode);

