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

use Catalyst\Kernel;

require_once realpath(implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), '..', 'vendor', 'autoload.php']));

/**
 * Prevent web entry point execution in CLI environment
 * and provide guidance on proper CLI execution method
 */
if (IS_CLI) {
    echo 'To run the project in a terminal instance (CLI) use "cli.php"' . NL . ' as the entry point and the execution command would be:' . NL;
    exit('php public/cli.php // from the project root directory' . NL);
}

// Bootstrap the application
$app = new Kernel();
try {

    $app->bootstrap();

    //ex(get_defined_constants(true)['user']);

    $app->run();

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . NL;
    exit(1);
}