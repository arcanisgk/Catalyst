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
 * LayoutManager component for the Catalyst Framework
 *
 */

namespace Catalyst\Framework\Core\View;

use Catalyst\Framework\Traits\SingletonTrait;
use Catalyst\Helpers\Log\Logger;
use Exception;

/**************************************************************************************
 * LayoutManager class for handling layouts in the view system
 *
 * Responsible for resolving and applying layouts to rendered views.
 * Centralizes layout-specific functionality that was previously mixed
 * with view rendering to respect the Single Responsibility Principle.
 *
 * @package Catalyst\Framework\Core\View;
 */
class LayoutManager
{
    use SingletonTrait;

    /**
     * ViewFinder instance for finding layout files
     *
     * @var ViewFinder
     */
    protected ViewFinder $viewFinder;

    /**
     * Default layout name
     *
     * @var string|null
     */
    protected ?string $defaultLayout = 'default';

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->viewFinder = ViewFinder::getInstance();
    }

    /**
     * Set the default layout
     *
     * @param string|null $layoutName Default layout name or null to disable
     * @return self For method chaining
     */
    public function setDefaultLayout(?string $layoutName): self
    {
        $this->defaultLayout = $layoutName;
        return $this;
    }

    /**
     * Get the default layout name
     *
     * @return string|null Default layout name or null if not set
     */
    public function getDefaultLayout(): ?string
    {
        return $this->defaultLayout;
    }

    /**
     * Apply a layout to the rendered view content
     *
     * @param string $content View content to wrap in layout
     * @param string|null $layoutName Layout name or null to use default
     * @param array $data Variables to pass to the layout
     * @return string Resulting content with applied layout
     * @throws Exception If layout file cannot be found or rendered
     */
    public function applyLayout(string $content, ?string $layoutName = null, array $data = []): string
    {
        // If no layout specified and no default, return content as is
        if ($layoutName === null && $this->defaultLayout === null) {
            return $content;
        }

        // Use specified layout or fall back to default
        $layoutToUse = $layoutName ?? $this->defaultLayout;

        // Find layout file
        $layoutFile = $this->viewFinder->findLayout($layoutToUse);

        if (!$layoutFile) {
            throw new Exception("Layout file not found: $layoutToUse");
        }

        // Make view content available to layout
        $data['viewContent'] = $content;

        // Capture the output from the layout
        return $this->renderLayout($layoutFile, $data);
    }

    /**
     * Render a layout file with the provided data
     *
     * @param string $layoutFile Full path to layout file
     * @param array $data Variables to extract in layout scope
     * @return string Rendered layout
     * @throws Exception If rendering fails
     */
    protected function renderLayout(string $layoutFile, array $data): string
    {
        try {
            // Start output buffering
            ob_start();

            // Extract variables to local scope
            extract($data, EXTR_SKIP);

            // Include the layout file
            include $layoutFile;

            // Return the captured output
            return ob_get_clean() ?: '';

        } catch (Exception $e) {
            // Clean up output buffer in case of error
            if (ob_get_level() > 0) {
                ob_end_clean();
            }

            // Log the error
            Logger::getInstance()->error('Layout rendering failed', [
                'layout' => $layoutFile,
                'error' => $e->getMessage()
            ]);

            // Re-throw with more context
            throw new Exception("Failed to render layout '$layoutFile': " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Check if a layout exists
     *
     * @param string $layoutName Layout name to check
     * @return bool True if layout exists
     */
    public function layoutExists(string $layoutName): bool
    {
        return $this->viewFinder->findLayout($layoutName) !== null;
    }

    /**
     * Get the full path to a layout
     *
     * @param string $layoutName Layout name
     * @return string|null Full path if found, null otherwise
     */
    public function getLayoutPath(string $layoutName): ?string
    {
        return $this->viewFinder->findLayout($layoutName);
    }

    /**
     * Create a default layout if it doesn't exist
     *
     * @param string $content Content for default layout
     * @return bool True if created, false if it already exists
     */
    public function createDefaultLayout(string $content): bool
    {
        if ($this->defaultLayout && !$this->layoutExists($this->defaultLayout)) {
            // Create directory if needed
            $directory = $this->viewFinder->getPaths()['framework'] . DS . 'layouts';

            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $layoutPath = $directory . DS . $this->defaultLayout . $this->viewFinder->getExtension();
            file_put_contents($layoutPath, $content);

            return true;
        }

        return false;
    }
}
