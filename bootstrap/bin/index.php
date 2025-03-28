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

use Catalyst\Framework\Core\Argument\Argument;
use Catalyst\Kernel;

require_once realpath(implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), '..', '..', 'vendor', 'autoload.php']));

// Bootstrap the application
$app = new Kernel();

try {
    $app->bootstrap();

    // Parse command line arguments
    $args = new Argument();

    // Register commands
    // Format: command:action => [handler class, method]
    $commands = [];

    // Get the command from arguments
    $command = $args->getCommand();

    if (empty($command)) {
        // List available commands if none specified
        echo "Available commands:\n";
        foreach (array_keys($commands) as $cmd) {
            echo "  $cmd\n";
        }
        echo "\nUse 'php cli.php command --help' for more information on a command.\n";
        exit(0);
    }

    // Execute the command if registered
    if (isset($commands[$command])) {
        [$handlerClass, $method] = $commands[$command];
        $handler = new $handlerClass();
        $handler->$method($args);
    } else {
        echo "Unknown command: $command\n";
        echo "Use 'php cli.php' to see available commands.\n";
        exit(1);
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . NL;
    exit(1);
}
