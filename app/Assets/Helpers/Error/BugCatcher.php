<?php

declare(strict_types=1);

/**************************************************************************************
 *
 * Catalyst PHP Framework
 * PHP Version 8.3 (Required).
 *
 * @package   Catalyst
 * @subpackage Assets
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
 * BugCatcher component for the Catalyst Framework
 *
 */

namespace Catalyst\Helpers\Error;

use Catalyst\Framework\Traits\SingletonTrait;

/**************************************************************************************
 * Class that handles capturing and displaying errors in the application.
 *
 * @package Catalyst\Helpers\Error;
 */
class BugCatcher
{

    use SingletonTrait;

    /**
     * Flag to track if the error handling system has been initialized
     *
     * @var bool
     */
    private bool $initialized = false;

    /**
     * Initialize the error handling system
     *
     * @return void
     */
    public function initialize(): void
    {
        // Prevent double initialization
        if ($this->initialized) {
            return;
        }

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

        // Mark as initialized
        $this->initialized = true;
    }

    /**
     * Check if the error handling system has been initialized
     *
     * @return bool True if initialized, false otherwise
     */
    /*public function isInitialized(): bool
    {
        return $this->initialized;
    }
    */
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

// Initialize BugCatcher if the class is loaded
// This serves as a fallback initialization when loaded through Composer's autoloader
if (defined('LOADED_BUG_CATCHER')) {
    // Only initialize if not already initialized
    if (!defined('INITIALIZED_BUG_CATCHER')) {
        define('INITIALIZED_BUG_CATCHER', true);
        BugCatcher::getInstance()->initialize();
    }
} else {
    // Direct initialization when loaded individually (should not happen in normal flow)
    define('LOADED_BUG_CATCHER', true);
    define('INITIALIZED_BUG_CATCHER', true);
    BugCatcher::getInstance()->initialize();
}
