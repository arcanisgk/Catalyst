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

use Exception;
use App\Assets\Framework\Traits\{SingletonTrait, OutputCleanerTrait};

/**************************************************************************************
 * Class that handles registered Shutdowns.
 *
 * @package App\Assets\Helpers\Error;
 */
class ShutdownHandler
{
    use SingletonTrait;

    use OutputCleanerTrait;

    /**
     * Shutdown handler. Captures fatal errors that would otherwise not be caught.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        // Get the last error
        $error = error_get_last();

        // Check if there was a fatal error
        if ($error !== null) {
            $this->cleanOutput();

            $trace = array_reverse(debug_backtrace());
            array_pop($trace);

            $errorArray = [
                'class' => 'ShutdownHandler',
                'type' => $this->getErrorType($error['type']),
                'description' => $error['message'],
                'file' => $error['file'],
                'line' => $error['line'],
                'trace' => $trace,
            ];

            $bug_output = BugOutput::getInstance();

            // Generate backtrace
            $errorArray['trace_msg'] = $bug_output->formatBacktrace($errorArray);

            // Display error
            $bug_output->display($errorArray);
        }
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
            E_PARSE => 'Parse Error',
            E_CORE_ERROR => 'Core Error',
            E_COMPILE_ERROR => 'Compile Error',
            E_USER_ERROR => 'User Error',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
        ];

        return $errorTypes[$errorLevel] ?? 'Fatal Error';
    }
}