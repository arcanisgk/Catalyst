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

namespace App\Assets\Helpers\Error;

use App\Assets\Framework\Traits\SingletonTrait;

/**************************************************************************************
 * Class that handles capturing and displaying errors in the application.
 *
 * @package App\Assets\Helpers\Error;
 */
class BugCatcher
{

    use SingletonTrait;

    /**
     * Initialize the error handling system
     *
     * @return void
     */
    public function initialize(): void
    {
        // Configure error display based on environment
        $this->configureErrorDisplay();

        // Register handlers
        register_shutdown_function([ShutdownHandler::getInstance(), 'handle']);
        set_exception_handler([ExceptionHandler::getInstance(), 'handle']);
        set_error_handler([ErrorHandler::getInstance(), 'handle']);

        // Start output buffering to capture any output before error occurs
        if (ob_get_level() === 0) {
            ob_start();
        }
    }

    /**
     * Configure PHP error display settings based on environment
     *
     * @return void
     */
    private function configureErrorDisplay(): void
    {
        if (IS_DEVELOPMENT) {
            ini_set('display_errors', '1');
            ini_set('display_startup_errors', '1');
        } else {
            ini_set('display_errors', '0');
            ini_set('display_startup_errors', '0');
        }
        error_reporting(E_ALL);
    }
}

if (!defined('BUG_CATCHER_LOADED')) {
    BugCatcher::getInstance()->initialize();
}
