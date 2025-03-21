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
use InvalidArgumentException;

/**************************************************************************************
 * ViewFinder class for locating view files across multiple paths
 *
 * Responsible for finding view files according to naming conventions
 * and configured search paths. Provides a central location for
 * view resolution logic separate from rendering and response handling.
 *
 * @package Catalyst\Framework\Core\View;
 */
class ViewFinder
{

    use SingletonTrait;

    /**
     * Registered view paths in priority order (first match wins)
     *
     * @var array<string, string>
     */
    protected array $paths = [];

    /**
     * View name extensions to search for
     *
     * @var string
     */
    protected string $extension = '.php';

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->initializeDefaultPaths();
    }

    /**
     * Set up default view paths
     *
     * @return void
     */
    protected function initializeDefaultPaths(): void
    {
        // Repository views take precedence over framework views
        $this->paths = [
            'template' => PD . DS . 'bootstrap' . DS . 'template',
            'framework' => PD . DS . 'app' . DS . 'Assets' . DS . 'Framework' . DS . 'Views',
            'solution' => PD . DS . 'app' . DS . 'Assets' . DS . 'Solution' . DS . 'Views',
            'repository' => PD . DS . 'app' . DS . 'Repository' . DS . 'Views'
        ];
    }

    /**
     * Add a view path to the finder
     *
     * @param string $name Unique identifier for this path
     * @param string $path Directory path
     * @param bool $prepend Whether to add at the start of the search list
     * @return self For method chaining
     */
    public function addPath(string $name, string $path, bool $prepend = false): self
    {
        if (!is_dir($path)) {
            throw new InvalidArgumentException("View path does not exist: $path");
        }

        if ($prepend) {
            // Add to the beginning of the array
            $this->paths = [$name => $path] + $this->paths;
        } else {
            // Add to the end of the array
            $this->paths[$name] = $path;
        }

        return $this;
    }

    /**
     * Get all registered view paths
     *
     * @return array View paths
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * Set the file extension for views
     *
     * @param string $extension File extension (with dot)
     * @return self For method chaining
     */
    public function setExtension(string $extension): self
    {
        // Ensure extension starts with a dot
        if ($extension && $extension[0] !== '.') {
            $extension = '.' . $extension;
        }

        $this->extension = $extension;
        return $this;
    }

    /**
     * Get the current file extension
     *
     * @return string Current file extension
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * Find a view file by name
     *
     * @param string $view View name (can use dot notation)
     * @return string|null Full path to view file or null if not found
     */
    public function find(string $view): ?string
    {

        //debug('Buscando vista', ['vista' => $view]);

        // Convert dots to directory separators
        $viewPath = str_replace('.', DS, $view);

        // Try finding the view using different conventions
        return $this->findViewFile($viewPath);
    }

    /**
     * Find a view file using multiple naming conventions
     *
     * @param string $viewPath View path with directory separators
     * @return string|null Full path to view file or null if not found
     */
    protected function findViewFile(string $viewPath): ?string
    {
        // 1. Try direct match (with extension)
        $file = $this->findInPaths($viewPath . $this->extension);
        if ($file) {
            return $file;
        }

        // 2. Try with capitalized directory name: 'contact' => 'Contact/index.php'
        if (!str_contains($viewPath, DS)) {
            $file = $this->findInPaths(ucfirst($viewPath) . DS . 'index' . $this->extension);
            if ($file) {
                return $file;
            }
        }

        // 3. Try with original case: 'contact' => 'contact/index.php'
        if (!str_contains($viewPath, DS)) {
            $file = $this->findInPaths($viewPath . DS . 'index' . $this->extension);
            if ($file) {
                return $file;
            }
        }

        // 4. For namespaced views, try index if only namespace given
        $lastSlashPos = strrpos($viewPath, DS);
        if ($lastSlashPos !== false) {
            $directory = substr($viewPath, 0, $lastSlashPos + 1);
            $file = $this->findInPaths($directory . 'index' . $this->extension);
            if ($file) {
                return $file;
            }
        }

        return null;
    }

    /**
     * Look for a file across all view paths
     *
     * @param string $file Relative file path to look for
     * @return string|null Full path if found, null otherwise
     */
    protected function findInPaths(string $file): ?string
    {
        foreach ($this->paths as $path) {
            $filePath = $path . DS . $file;
            if (file_exists($filePath) && is_readable($filePath)) {
                return $filePath;
            }
        }

        return null;
    }

    /**
     * Check if a view exists
     *
     * @param string $view View name
     * @return bool True if view exists
     */
    public function exists(string $view): bool
    {
        return $this->find($view) !== null;
    }

    /**
     * Find a file in a specific subdirectory of view paths
     *
     * @param string $subDir Subdirectory within view paths
     * @param string $name File name to find
     * @return string|null Full path if found, null otherwise
     */
    public function findInSubdirectory(string $subDir, string $name): ?string
    {
        // Convert dots to directory separators for file name
        $name = str_replace('.', DS, $name);

        // Look for file in the subdirectory of all view paths
        foreach ($this->paths as $path) {
            $filePath = $path . DS . $subDir . DS . $name . $this->extension;

            if (file_exists($filePath) && is_readable($filePath)) {
                return $filePath;
            }
        }

        return null;
    }

    /**
     * Find a layout file
     *
     * @param string $name Layout name
     * @return string|null Full path if found, null otherwise
     */
    public function findLayout(string $name): ?string
    {

        return $this->findInSubdirectory('layouts', $name);
    }

    /**
     * Find a partial file
     *
     * @param string $name Partial name
     * @return string|null Full path if found, null otherwise
     */
    public function findPartial(string $name): ?string
    {
        return $this->findInSubdirectory('partials', $name);
    }
}