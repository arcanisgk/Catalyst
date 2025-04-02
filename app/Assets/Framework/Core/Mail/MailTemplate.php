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
 * MailTemplate component for the Catalyst Framework
 *
 */

namespace Catalyst\Framework\Core\Mail;

use Catalyst\Framework\Core\Exceptions\MailException;
use RuntimeException;
use Throwable;

/**************************************************************************************
 * Email template processor
 *
 * Handles loading and processing of email templates with variable substitution.
 *
 * @package Catalyst\Framework\Core\Mail
 */
class MailTemplate
{
    /**
     * @var string Base path for template files
     */
    protected string $basePath;

    /**
     * Constructor
     *
     * @param string|null $basePath Base path for template files (optional)
     */
    public function __construct(?string $basePath = null)
    {
        $this->basePath = $basePath ?? implode(DS, [PD, 'bootstrap', 'template', 'email']);
    }

    /**
     * Load and render a template by name
     *
     * @param string $name MailTemplate name
     * @param array $variables Variables to replace
     * @return array Rendered HTML and text versions
     * @throws MailException If template cannot be found or processed
     */
    public function render(string $name, array $variables = []): array
    {
        $htmlPath = $this->getTemplatePath($name, 'html');
        $textPath = $this->getTemplatePath($name, 'text');

        $result = [];

        // Try to load HTML template
        if (file_exists($htmlPath)) {
            $result['html'] = $this->processTemplate($htmlPath, $variables);
        }

        // Try to load text template
        if (file_exists($textPath)) {
            $result['text'] = $this->processTemplate($textPath, $variables);
        }

        // If we don't have either template, throw an exception
        if (empty($result)) {
            throw MailException::templateError($name, 'MailTemplate not found');
        }

        // If we only have HTML, generate a text version
        if (isset($result['html']) && !isset($result['text'])) {
            $result['text'] = strip_tags($result['html']);
        }

        return $result;
    }

    /**
     * Render a template from a direct file path
     *
     * @param string $path MailTemplate file path
     * @param array $variables Variables to replace
     * @return array Rendered HTML and text versions
     * @throws MailException If template cannot be found or processed
     */
    public function renderFromPath(string $path, array $variables = []): array
    {
        if (!file_exists($path)) {
            throw MailException::templateError($path, 'MailTemplate file not found');
        }

        $content = $this->processTemplate($path, $variables);

        // Determine template type by extension or content
        $pathInfo = pathinfo($path);
        $isHtml = false;

        if (isset($pathInfo['extension'])) {
            $isHtml = in_array(strtolower($pathInfo['extension']), ['html', 'htm', 'php']);
        } else {
            // Check content for HTML tags
            $isHtml = preg_match('/<[^>]+>/', $content);
        }

        if ($isHtml) {
            return [
                'html' => $content,
                'text' => strip_tags($content)
            ];
        } else {
            return [
                'text' => $content
            ];
        }
    }

    /**
     * Process a template file with variable substitution
     *
     * @param string $path MailTemplate file path
     * @param array $variables Variables to replace
     * @return string Processed template content
     * @throws MailException If template cannot be processed
     */
    protected function processTemplate(string $path, array $variables = []): string
    {
        try {
            // Extract variables for use in the template
            extract($variables, EXTR_SKIP);

            // Start output buffering
            ob_start();

            // Include the template
            include $path;

            // Get the buffered content
            $content = ob_get_clean();

            if ($content === false) {
                throw new RuntimeException('Failed to process template');
            }

            return $content;
        } catch (Throwable $e) {
            throw MailException::templateError($path, $e->getMessage());
        }
    }

    /**
     * Get the full path to a template file
     *
     * @param string $name MailTemplate name
     * @param string $type MailTemplate type (html/text)
     * @return string Full path to template file
     */
    protected function getTemplatePath(string $name, string $type): string
    {
        $extension = $type === 'html' ? '.php' : '.txt';

        // Check for type-specific file
        $path = $this->basePath . DS . $name . '.' . $type . $extension;
        if (file_exists($path)) {
            return $path;
        }

        // Fall back to default extensions
        return $this->basePath . DS . $name . $extension;
    }

    /**
     * Set the base path for templates
     *
     * @param string $path Base path
     * @return self For method chaining
     */
    public function setBasePath(string $path): self
    {
        $this->basePath = $path;
        return $this;
    }
}