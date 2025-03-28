<?php

/**
 * CLI Runner for Catalyst Framework
 * This script executes index.php with custom php.ini configuration
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

