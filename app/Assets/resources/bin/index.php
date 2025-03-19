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

use App\Kernel;

require_once realpath(implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), '..', '..', '..', '..', 'vendor', 'autoload.php']));

// Bootstrap the application
$app = new Kernel();
try {
    $app->bootstrap();

    //ex(get_defined_constants(true)['user']);

    //$app->run();
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . NL;
    exit(1);
}


