<?php

/**
 * CLI Runner for Catalyst Framework
 * This script executes index.php with custom php.ini configuration
 */

// Get the base directory of the project
$baseDir = dirname(__FILE__, 2);

// Build paths in a platform-independent way
$phpIniPath = $baseDir . DIRECTORY_SEPARATOR . 'php.ini';
$indexPath = implode(DIRECTORY_SEPARATOR, [$baseDir, 'bootstrap', 'bin', 'index.php']);

// Get PHP executable path
$phpExecutable = PHP_BINARY;

// Build the command
$command = escapeshellcmd("$phpExecutable -c \"$phpIniPath\" \"$indexPath\"");

// Output what we're doing
echo "Executing: $command\n";

// Execute the command and pass through all output
passthru($command, $returnCode);

// Return the same exit code from the subprocess
exit($returnCode);
