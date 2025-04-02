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
 * ErrorHandler component for the Catalyst Framework
 *
 */

namespace Catalyst\Helpers\Error;

use Exception;
use Catalyst\Framework\Traits\{SingletonTrait, OutputCleanerTrait};

/**************************************************************************************
 * Class that handles registered Errors.
 *
 * @package Catalyst\Helpers\Error;
 */
class ErrorHandler
{
    use SingletonTrait;

    use OutputCleanerTrait;

    /**
     * Error handler. Captures and handles errors generated in the application.
     *
     * @param int $errorLevel Error level.
     * @param string $errorDesc Error description.
     * @param string $errorFile File where the error occurred.
     * @param int $errorLine Line where the error occurred.
     * @return bool True to prevent default PHP error handler
     * @throws Exception
     */
    public function handle(int $errorLevel, string $errorDesc, string $errorFile, int $errorLine): bool
    {
        // Only handle errors that match the error_reporting level
        if (!(error_reporting() & $errorLevel)) {
            return false;
        }

        // Clean any output already sent
        $this->cleanOutput();

        // Map error level to text description
        $errorType = $this->getErrorType($errorLevel);

        $trace = array_reverse(debug_backtrace());
        array_pop($trace);
        $trace = array_reverse($trace);

        // Prepare error data
        $errorArray = [
            'class' => 'ErrorHandler',
            'type' => $errorType,
            'description' => $errorDesc,
            'file' => $errorFile,
            'line' => $errorLine,
            'trace' => $trace,
        ];

        $bug_output = BugOutput::getInstance();

        // Generate backtrace
        $errorArray['trace_msg'] = $bug_output->formatBacktrace($errorArray);

        // Display error
        $bug_output->display($errorArray);

        // Return true to prevent default error handler
        return true;
    }

    /**
     * Map PHP error level to text description
     *
     * @param int $errorLevel PHP error level
     * @return string Text description of error level
     */
    private function getErrorType(int $errorLevel): string
    {
        $errorTypes = [
            E_ERROR => 'Fatal Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict Standards',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated',
        ];

        return $errorTypes[$errorLevel] ?? 'Unknown Error';
    }
}