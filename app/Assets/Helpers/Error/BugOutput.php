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

namespace Catalyst\Helpers\Error;

use Catalyst\Framework\Traits\SingletonTrait;
use Catalyst\Helpers\ToolBox\DrawBox;
use Closure;
use Exception;

/**************************************************************************************
 * Class to handle output of errors caught by BugCatcher
 *
 * @package Catalyst\Helpers\Error;
 */
class BugOutput
{
    use SingletonTrait;

    /**
     * Path to error templates
     */
    private const string TEMPLATE_PATH = PD . '/app/Assets/Framework/Views/Errors/';

    /**
     * Output the error information based on environment (CLI or Web)
     *
     * @param array $errorData Error data to display
     * @return void
     * @throws Exception
     */
    public function display(array $errorData): void
    {
        $errorData['micro_time'] = microtime(true);

        BugLogger::logError($errorData);

        if (IS_CLI) {
            $this->displayCLI($errorData);
        } else {
            $this->displayWeb($errorData);
        }
    }

    /**
     * Display error information in CLI mode
     *
     * @param array $errorData Error data to display
     * @return void
     * @throws Exception
     */
    private function displayCLI(array $errorData): void
    {
        $output = $this->formatCliOutput($errorData);
        $drawBox = DrawBox::getInstance();

        // Display formatted error output using DrawBox with error styling (red, highlighted)
        echo $drawBox->draw($output, [
            'headerLines' => 1,
            'footerLines' => 0,
            'highlight' => true,
            'maxWidth' => 0,
            'style' => 2, // Error style (red)
            'isError' => true
        ]);
    }

    /**
     * Format error data for CLI output
     *
     * @param array $errorData Error data to format
     * @return string Formatted error information
     */
    private function formatCliOutput(array $errorData): string
    {
        $output = '';

        if (IS_DEVELOPMENT) {
            $output .= "Class: {$errorData['class']}" . NL
                . "Description:" . NL . "{$errorData['description']}" . NL . NL
                . "File: {$errorData['file']}" . NL
                . "Line: {$errorData['line']}" . ' ' . "Type: {$errorData['type']}" . ' ' . "Time: {$errorData['micro_time']}" . NL . NL
                . "Backtrace:" . NL . "{$errorData['trace_msg']}" . NL;
        } else {
            $output .= "Micro Time: {$errorData['micro_time']}";
        }

        return $output;
    }

    /**
     * Display error information in web mode using templates
     *
     * @param array $errorData Error data to display
     * @return void
     */
    private function displayWeb(array $errorData): void
    {
        // Determine which template to use based on environment
        $templateFile = IS_DEVELOPMENT ? 'handler_error.php' : 'handler_error_no.php';
        $templatePath = self::TEMPLATE_PATH . $templateFile;

        // Generate source code view for development mode
        $source = '';
        if (IS_DEVELOPMENT && isset($errorData['file']) && isset($errorData['line'])) {
            $source = $this->getCodeSnippet($errorData['file'], $errorData['line']);
        }

        // Extract error data to make them available in the template
        extract(['errorArray' => $errorData, 'source' => $source]);

        // Render the template
        if (file_exists($templatePath)) {
            include $templatePath;
        } else {
            // Fallback if template is missing
            echo '<h1>Error</h1>';
            echo '<p>Error template not found at: ' . htmlspecialchars($templatePath) . '</p>';
            if (IS_DEVELOPMENT) {
                echo '<pre>' . print_r($errorData, true) . '</pre>';
            }
        }
    }

    /**
     * Get code snippet from the file where the error occurred
     *
     * @param string $file File path
     * @param int $line Line number
     * @param int $contextLines Number of lines to show before and after the error line
     * @return string HTML formatted code snippet
     */
    private function getCodeSnippet(string $file, int $line, int $contextLines = 5): string
    {
        if (!file_exists($file) || !is_readable($file)) {
            return '<pre>Source code not available.</pre>';
        }

        $fileContent = file($file);
        if (!$fileContent) {
            return '<pre>Unable to read source file.</pre>';
        }

        $startLine = max(0, $line - $contextLines - 1);
        $endLine = min(count($fileContent) - 1, $line + $contextLines - 1);

        $html = '<pre><code>';
        for ($i = $startLine; $i <= $endLine; $i++) {
            $lineNumber = $i + 1;
            $codeLine = htmlspecialchars($fileContent[$i]);

            // Highlight the error line
            if ($lineNumber == $line) {
                $html .= "<span style='background-color:#ffdddd; font-weight:bold;'>";
                $html .= "$lineNumber: $codeLine</span>";
            } else {
                $html .= "$lineNumber: $codeLine";
            }
        }
        $html .= '</code></pre>';

        return $html;
    }

    /**
     * Generate formatted backtrace
     *
     * @param array $errorData Error data containing trace information
     * @return string Formatted backtrace string
     */
    public function formatBacktrace(array $errorData): string
    {
        $backtraceMessage = [];
        $traceData = $errorData['trace'] ?? [];

        if (!empty($traceData)) {
            foreach ($traceData as $track) {

                $args = '';

                if (isset($track['args']) && !empty($track['args'])) {
                    $args = $this->formatArguments($track['args']);
                }

                $route = $this->getRouteDescription($track);
                $backtraceMessage[] = sprintf('%s%s(%s)', $route, $track['function'], $args);
            }
        } else {
            $backtraceMessage[] = sprintf('No backtrace data in the %s.', $errorData['class']);
        }

        return implode(NL, $backtraceMessage);
    }

    /**
     * Format arguments for display.
     *
     * @param array $args Arguments array.
     *
     * @return string Formatted arguments.
     */
    private function formatArguments(array $args): string
    {
        $formattedArgs = [];

        foreach ($args as $arg) {
            if (is_array($arg)) {
                $formattedArgs[] = 'Array';
            } elseif (is_object($arg)) {
                if ($arg instanceof Closure) {
                    $formattedArgs[] = 'Closure';
                } else {
                    $formattedArgs[] = get_class($arg);
                }
            } else {
                $formattedArgs[] = is_string($arg) ? "'" . $arg . "'" : (string)$arg;
            }
        }

        return implode(',', $formattedArgs);
    }

    /**
     * Get description of the route (file and line) or magic call method.
     *
     * @param array $track Stack trace information.
     *
     * @return string Route description.
     */
    private function getRouteDescription(array $track): string
    {
        if (!isset($track['file']) && !isset($track['line'])) {
            return sprintf('Magic Call Method: (%s)->', $track['class']);
        }

        return sprintf('%s %s calling Method: ', $track['file'], $track['line']);
    }
}