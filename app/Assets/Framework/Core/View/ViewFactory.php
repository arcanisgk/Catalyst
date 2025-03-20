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

namespace Catalyst\Framework\Core\View;

use Catalyst\Framework\Traits\SingletonTrait;
use Catalyst\Helpers\Log\Logger;
use Exception;

/**************************************************************************************
 * ViewFactory class for coordinating the view subsystem
 *
 * Acts as a facade for the view rendering system by coordinating ViewFinder,
 * LayoutManager, and ViewRenderer components. Provides a clean, unified interface
 * for controllers and response classes to work with views.
 *
 * @package Catalyst\Framework\Core\View;
 */
class ViewFactory
{
    use SingletonTrait;

    /**
     * ViewFinder instance for locating view files
     *
     * @var ViewFinder
     */
    protected ViewFinder $viewFinder;

    /**
     * LayoutManager instance for handling layouts
     *
     * @var LayoutManager
     */
    protected LayoutManager $layoutManager;

    /**
     * ViewRenderer instance for rendering views
     *
     * @var ViewRenderer
     */
    protected ViewRenderer $viewRenderer;

    /**
     * Logger instance
     *
     * @var Logger
     */
    protected Logger $logger;

    /**
     * Shared data available to all views
     *
     * @var array
     */
    protected array $sharedData = [];

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->viewFinder = ViewFinder::getInstance();
        $this->layoutManager = LayoutManager::getInstance();
        $this->viewRenderer = ViewRenderer::getInstance();
        $this->logger = Logger::getInstance();
    }

    /**
     * Make a view with the given data
     *
     * @param string $view View name
     * @param array $data View data
     * @param string|null $layout Layout name or null for no layout
     * @return string Rendered view content
     * @throws Exception If view cannot be found or rendered
     */
    public function make(string $view, array $data = [], ?string $layout = null): string
    {
        try {
            // Find the view file
            $viewFile = $this->viewFinder->find($view);

            // If view not found, throw exception
            if (!$viewFile) {
                throw new Exception("View not found: {$view}");
            }

            // Merge shared data with view-specific data
            $viewData = array_merge($this->sharedData, $data);

            // Add helper functions to view data
            $viewData = $this->addHelperFunctions($viewData);

            // Render the view
            $content = $this->viewRenderer->render($viewFile, $viewData);

            // Apply layout if specified
            if ($layout !== null) {
                $content = $this->layoutManager->applyLayout($content, $layout, $viewData);
            }

            return $content;

        } catch (Exception $e) {
            // Log the error
            $this->logger->error('View creation failed', [
                'view' => $view,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw with more context
            throw new Exception("Failed to create view '{$view}': " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Render a partial view
     *
     * @param string $partial Partial name
     * @param array $data Partial-specific data
     * @param array $parentData Parent view data
     * @return string Rendered partial content
     * @throws Exception If partial cannot be found or rendered
     */
    public function partial(string $partial, array $data = [], array $parentData = []): string
    {
        // Find the partial file
        $partialFile = $this->viewFinder->findPartial($partial);

        // If partial not found, throw exception
        if (!$partialFile) {
            throw new Exception("Partial not found: {$partial}");
        }

        // Add helper functions to data
        $data = $this->addHelperFunctions($data);

        // Render the partial
        return $this->viewRenderer->renderPartial($partialFile, $data, $parentData);
    }

    /**
     * Check if a view exists
     *
     * @param string $view View name
     * @return bool True if view exists
     */
    public function exists(string $view): bool
    {
        return $this->viewFinder->exists($view);
    }

    /**
     * Share data with all views
     *
     * @param string $key Data key
     * @param mixed $value Data value
     * @return self For method chaining
     */
    public function share(string $key, mixed $value): self
    {
        $this->sharedData[$key] = $value;
        return $this;
    }

    /**
     * Share multiple data items with all views
     *
     * @param array $data Data to share
     * @return self For method chaining
     */
    public function shareMany(array $data): self
    {
        $this->sharedData = array_merge($this->sharedData, $data);
        return $this;
    }

    /**
     * Get all shared data
     *
     * @return array Shared data
     */
    public function getSharedData(): array
    {
        return $this->sharedData;
    }

    /**
     * Get the ViewFinder instance
     *
     * @return ViewFinder ViewFinder instance
     */
    public function getFinder(): ViewFinder
    {
        return $this->viewFinder;
    }

    /**
     * Get the LayoutManager instance
     *
     * @return LayoutManager LayoutManager instance
     */
    public function getLayoutManager(): LayoutManager
    {
        return $this->layoutManager;
    }

    /**
     * Get the ViewRenderer instance
     *
     * @return ViewRenderer ViewRenderer instance
     */
    public function getRenderer(): ViewRenderer
    {
        return $this->viewRenderer;
    }

    /**
     * Add helper functions to view data
     *
     * @param array $data View data
     * @return array Data with added helper functions
     */
    protected function addHelperFunctions(array $data): array
    {
        // Add translation function (uses global helper function)
        $data['t'] = function (string $key, array $replacements = []) {
            return t($key, $replacements);
        };

        // Add include function for partials
        $data['include'] = function (string $partial, array $partialData = []) use ($data) {
            return $this->partial($partial, $partialData, $data);
        };

        // Add asset URL helper if the function exists
        if (function_exists('asset')) {
            $data['asset'] = function (string $path) {
                return asset($path);
            };
        }

        // Add route URL helper if the function exists
        if (function_exists('route')) {
            $data['url'] = function (string $name, array $parameters = [], bool $absolute = false) {
                return route($name, $parameters, $absolute);
            };
        }

        // Add other helper functions as needed...

        return $data;
    }

    /**
     * Add a view path
     *
     * @param string $name Path name
     * @param string $path Directory path
     * @param bool $prepend Whether to prepend to the path list
     * @return self For method chaining
     */
    public function addPath(string $name, string $path, bool $prepend = false): self
    {
        $this->viewFinder->addPath($name, $path, $prepend);
        return $this;
    }

    /**
     * Set the default layout
     *
     * @param string|null $layout Default layout name or null to disable
     * @return self For method chaining
     */
    public function setDefaultLayout(?string $layout): self
    {
        $this->layoutManager->setDefaultLayout($layout);
        return $this;
    }

    /**
     * Create a default layout if it doesn't exist
     *
     * @param string $content Default layout content
     * @return bool True if created, false if already exists
     */
    public function createDefaultLayout(string $content): bool
    {
        return $this->layoutManager->createDefaultLayout($content);
    }
}
