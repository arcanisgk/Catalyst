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
 * ExceptionHandler component for the Catalyst Framework
 *
 */

namespace Catalyst\Helpers\Error;

use Exception;
use Catalyst\Framework\Traits\{SingletonTrait, OutputCleanerTrait};
use Throwable;

/**************************************************************************************
 * Class that handles registered Exceptions.
 *
 * @package Catalyst\Helpers\Error;
 */
class ExceptionHandler
{
    use SingletonTrait;

    use OutputCleanerTrait;

    /**
     * Exception handler. Captures and handles exceptions thrown in the application.
     *
     * @param Throwable $exception The captured exception.
     * @return void
     * @throws Exception
     */
    public function handle(Throwable $exception): void
    {
        // Clean any output already sent
        $this->cleanOutput();

        // Prepare error data
        $errorArray = [
            'class' => 'ExceptionHandler',
            'type' => ($exception->getCode() === 0 ? 'Uncaught Exception' : "Exception (Code: {$exception->getCode()})"),
            'description' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace(),
        ];

        $bug_output = BugOutput::getInstance();

        // Generate backtrace
        $errorArray['trace_msg'] = $bug_output->formatBacktrace($errorArray);

        // Display error
        $bug_output->display($errorArray);
    }
}