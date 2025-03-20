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

namespace Catalyst\Framework\Core\Response;

use Catalyst\Framework\Core\View\ViewFactory;
use Catalyst\Helpers\Log\Logger;
use Exception;
use InvalidArgumentException;

/**************************************************************************************
 * ViewResponse class for view/template-based responses
 *
 * Specializes the base Response class for HTML content generated from views
 * with proper content-type headers. Delegates view rendering to ViewFactory
 * to maintain separation of concerns.
 *
 * @package Catalyst\Framework\Core\Response;
 */
class ViewResponse extends Response
{
    /**
     * View name to render
     *
     * @var string
     */
    protected string $view;

    /**
     * Data to pass to the view
     *
     * @var array
     */
    protected array $data;

    /**
     * Layout to wrap the view in (if using layouts)
     *
     * @var string|null
     */
    protected ?string $layout = null;

    /**
     * Language for this response
     *
     * @var string
     */
    protected string $language = 'en';

    /**
     * Create a new view response
     *
     * @param string $view The view name to render
     * @param array $data Data to pass to the view
     * @param int $status The HTTP status code
     * @param array $headers Array of HTTP headers
     * @param string $charset Response charset
     * @throws InvalidArgumentException If view name is invalid
     */
    public function __construct(
        string $view,
        array  $data = [],
        int    $status = 200,
        array  $headers = [],
        string $charset = 'UTF-8'
    )
    {
        if (empty($view)) {
            throw new InvalidArgumentException('View name cannot be empty');
        }

        $this->view = $view;
        $this->data = $data;

        // Set default language if available in environment
        if (defined('DEF_LANG')) {
            $this->language = DEF_LANG;
        } else {
            $this->language = 'en';
        }


        // Set HTML-specific headers
        $headers['Content-Type'] = $headers['Content-Type'] ?? 'text/html; charset=' . $charset;

        // Initialize with empty content - will be rendered on send
        parent::__construct('', $status, $headers, $charset);
    }

    /**
     * Set the view to render
     *
     * @param string $view View name
     * @return self For method chaining
     */
    public function setView(string $view): self
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Get the view name
     *
     * @return string View name
     */
    public function getView(): string
    {
        return $this->view;
    }

    /**
     * Set the data to pass to the view
     *
     * @param array $data View data
     * @return self For method chaining
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get the view data
     *
     * @return array View data
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Add a single data item to the view
     *
     * @param string $key Data key
     * @param mixed $value Data value
     * @return self For method chaining
     */
    public function with(string $key, mixed $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Set the layout to use
     *
     * @param string|null $layout Layout name or null to disable layout
     * @return self For method chaining
     */
    public function setLayout(?string $layout): self
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * Get the layout name
     *
     * @return string|null Layout name
     */
    public function getLayout(): ?string
    {
        return $this->layout;
    }

    /**
     * Set the language for this response
     *
     * @param string $language Language code
     * @return self For method chaining
     */
    public function setLanguage(string $language): self
    {
        $this->language = $language;
        return $this;
    }

    /**
     * Get the language code
     *
     * @return string Language code
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * Render the view and set as response content
     *
     * @return self For method chaining
     * @throws Exception
     */
    protected function renderView(): self
    {
        try {
            // Get ViewFactory instance
            $viewFactory = ViewFactory::getInstance();

            // Add language information to the view data
            $this->data['currentLanguage'] = $this->language;

            // Get rendered content from ViewFactory
            $content = $viewFactory->make($this->view, $this->data, $this->layout);
            $this->setContent($content);
        } catch (Exception $e) {
            Logger::getInstance()->error('View rendering failed', [
                'view' => $this->view,
                'error' => $e->getMessage()
            ]);

            // Set error message as content if view rendering fails
            $errorContent = IS_DEVELOPMENT
                ? "Error rendering view '{$this->view}': " . $e->getMessage()
                : 'An error occurred while processing your request.';

            $this->setContent($errorContent);
            $this->setStatusCode(500);
        }

        return $this;
    }

    /**
     * Send the response to the client
     *
     * Overrides the parent send method to ensure view is rendered before sending
     *
     * @return self For method chaining
     * @throws Exception
     */
    public function send(): self
    {
        // Render the view if we haven't sent the response yet
        if (!$this->isSent()) {
            $this->renderView();
        }

        // Call parent send method
        return parent::send();
    }

    /**
     * Get the response content
     *
     * Overrides the parent method to ensure view is rendered before returning content
     *
     * @return string Response content
     * @throws Exception
     */
    public function getContent(): string
    {
        // Render the view if content is empty
        if (empty(parent::getContent())) {
            $this->renderView();
        }

        return parent::getContent();
    }

    /**
     * Create a new view response with a specific layout
     *
     * @param string $view View name
     * @param array $data View data
     * @param string $layout Layout name
     * @param int $status HTTP status code
     * @param array $headers Response headers
     * @return static New view response instance
     */
    public static function withLayout(
        string $view,
        array  $data = [],
        string $layout = 'default',
        int    $status = 200,
        array  $headers = []
    ): static
    {
        $instance = new static($view, $data, $status, $headers);
        $instance->setLayout($layout);
        return $instance;
    }

    /**
     * Create a view response with a specific language
     *
     * @param string $view View name
     * @param array $data View data
     * @param string $language Language code
     * @param int $status HTTP status code
     * @param array $headers Response headers
     * @return static New view response instance
     */
    public static function withLanguage(
        string $view,
        array  $data = [],
        string $language = 'en',
        int    $status = 200,
        array  $headers = []
    ): static
    {
        $instance = new static($view, $data, $status, $headers);
        $instance->setLanguage($language);
        return $instance;
    }

    /**
     * Create a view response with both layout and language specified
     *
     * @param string $view View name
     * @param array $data View data
     * @param string $layout Layout name
     * @param string $language Language code
     * @param int $status HTTP status code
     * @param array $headers Response headers
     * @return static New view response instance
     */
    public static function localized(
        string $view,
        array  $data = [],
        string $layout = 'default',
        string $language = 'en',
        int    $status = 200,
        array  $headers = []
    ): static
    {
        $instance = new static($view, $data, $status, $headers);
        $instance->setLayout($layout);
        $instance->setLanguage($language);
        return $instance;
    }
}