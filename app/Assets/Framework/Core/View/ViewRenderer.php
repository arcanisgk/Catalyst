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

namespace App\Assets\Framework\Core\View;

use App\Assets\Framework\Traits\SingletonTrait;
use App\Assets\Helpers\Log\Logger;
use Exception;
use RuntimeException;

/**************************************************************************************
 * ViewRenderer class for rendering view files with data
 *
 * Responsible for the actual rendering process of views, handling data extraction,
 * output buffering, and error management during rendering. This class focuses
 * solely on the rendering process, without concern for file location or
 * HTTP response details.
 *
 * @package App\Assets\Framework\Core\View;
 */
class ViewRenderer
{
    use SingletonTrait;

    /**
     * Logger instance
     *
     * @var Logger
     */
    protected Logger $logger;

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->logger = Logger::getInstance();
    }

    /**
     * Render a view file with the provided data
     *
     * @param string $viewFile Full path to view file
     * @param array $data Data to make available to the view
     * @return string Rendered view content
     * @throws Exception If rendering fails
     */
    public function render(string $viewFile, array $data = []): string
    {
        // Verify file exists and is readable
        if (!file_exists($viewFile)) {
            throw new RuntimeException("View file not found: {$viewFile}");
        }

        if (!is_readable($viewFile)) {
            throw new RuntimeException("View file not readable: {$viewFile}");
        }

        try {
            // Start output buffering
            ob_start();

            // Extract data to make variables available in view scope
            extract($data, EXTR_SKIP);

            // Include the view file for rendering
            include $viewFile;

            // Get the rendered content
            $renderedContent = ob_get_clean();

            // Check if rendering produced content
            if ($renderedContent === false) {
                throw new RuntimeException("Failed to capture view output for: {$viewFile}");
            }

            return $renderedContent;

        } catch (Exception $e) {
            // Clean up output buffer in case of error
            if (ob_get_level() > 0) {
                ob_end_clean();
            }

            // Log rendering error
            $this->logger->error('View rendering failed', [
                'view' => $viewFile,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw with more context
            throw new RuntimeException(
                "Error rendering view '{$viewFile}': " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Render a string template with data
     *
     * Useful for rendering small inline templates or user-defined templates
     *
     * @param string $template Template string to render
     * @param array $data Data to make available to the template
     * @return string Rendered template content
     * @throws Exception If rendering fails
     */
    public function renderString(string $template, array $data = []): string
    {
        try {
            // Create a temporary file for the template
            $tempFile = tempnam(sys_get_temp_dir(), 'catalyst_template_');
            file_put_contents($tempFile, $template);

            // Render the template
            $result = $this->render($tempFile, $data);

            // Clean up the temporary file
            unlink($tempFile);

            return $result;

        } catch (Exception $e) {
            // Log rendering error
            $this->logger->error('String template rendering failed', [
                'error' => $e->getMessage(),
                'template_length' => strlen($template)
            ]);

            // Re-throw with more context
            throw new RuntimeException(
                "Error rendering string template: " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Render a partial view
     *
     * @param string $partialFile Full path to partial view file
     * @param array $data Data to make available to the partial
     * @param array $parentData Parent view data that might be needed
     * @return string Rendered partial content
     * @throws Exception If rendering fails
     */
    public function renderPartial(string $partialFile, array $data = [], array $parentData = []): string
    {
        // Merge parent data with partial-specific data (partial data takes precedence)
        $mergedData = array_merge($parentData, $data);

        // Render the partial using the merged data
        return $this->render($partialFile, $mergedData);
    }

    /**
     * Evaluate PHP code within a controlled scope with data extraction
     *
     * @param string $code PHP code to evaluate
     * @param array $data Data to extract into the evaluation scope
     * @return mixed Result of the evaluation
     * @throws Exception If evaluation fails
     */
    public function evaluateCode(string $code, array $data = []): mixed
    {
        try {
            // Extract data to local scope
            extract($data, EXTR_SKIP);

            // Evaluate the code within a closure for scope isolation
            return eval('?>' . $code);

        } catch (Exception $e) {
            // Log evaluation error
            $this->logger->error('Code evaluation failed', [
                'error' => $e->getMessage(),
                'code_length' => strlen($code)
            ]);

            // Re-throw with more context
            throw new RuntimeException(
                "Error evaluating code: " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Get a snippet of code from a file for error reporting
     *
     * @param string $file File path
     * @param int $line Line number
     * @param int $context Number of context lines before and after
     * @return string Code snippet
     */
    public function getCodeSnippet(string $file, int $line, int $context = 3): string
    {
        if (!file_exists($file) || !is_readable($file)) {
            return "File not accessible: $file";
        }

        $lines = file($file);
        if (!$lines) {
            return "Could not read file: $file";
        }

        $start = max(0, $line - $context - 1);
        $end = min(count($lines) - 1, $line + $context - 1);

        $snippet = '';
        for ($i = $start; $i <= $end; $i++) {
            $currentLine = $i + 1;
            $marker = ($currentLine === $line) ? '> ' : '  ';
            $snippet .= sprintf(
                "%s%3d: %s",
                $marker,
                $currentLine,
                $lines[$i]
            );
        }

        return $snippet;
    }
}
