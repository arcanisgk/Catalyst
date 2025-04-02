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
 * DumperConfig component for the Catalyst Framework
 *
 */

namespace Catalyst\Helpers\Debug;

/**************************************************************************************
 * DumperConfig class for managing dumper configuration
 *
 * This class stores and manages configuration settings for the Dumper system.
 * It handles settings like maximum string length, maximum array/object children,
 * maximum nesting depth, and other configuration options.
 *
 * @package Catalyst\Helpers\Debug;
 */
class DumperConfig
{
    /**
     * Maximum string length in output
     */
    private int $maxStrLength = 150;

    /**
     * Maximum array/object children to show
     */
    private int $maxChildren = 50;

    /**
     * Maximum nesting level
     */
    private int $maxDepth = 10;

    /**
     * Whether to show the floating button in HTML mode
     */
    private bool $showFloatingButton = true;

    /**
     * Whether to initially expand arrays and objects
     */
    private bool $initiallyExpanded = true;

    /**
     * Selected color theme
     */
    private string $colorTheme = 'dark';

    /**
     * Constructor
     *
     * @param array $options Optional configuration options
     */
    public function __construct(array $options = [])
    {
        // Apply custom options if provided
        if (!empty($options)) {
            $this->applyOptions($options);
        }
    }

    /**
     * Apply configuration options
     *
     * @param array $options Configuration options
     * @return void
     */
    public function applyOptions(array $options): void
    {
        // Apply each option if it exists
        if (isset($options['maxStrLength'])) {
            $this->setMaxStrLength($options['maxStrLength']);
        }

        if (isset($options['maxChildren'])) {
            $this->setMaxChildren($options['maxChildren']);
        }

        if (isset($options['maxDepth'])) {
            $this->setMaxDepth($options['maxDepth']);
        }

        if (isset($options['showFloatingButton'])) {
            $this->setShowFloatingButton($options['showFloatingButton']);
        }

        if (isset($options['initiallyExpanded'])) {
            $this->setInitiallyExpanded($options['initiallyExpanded']);
        }

        if (isset($options['colorTheme'])) {
            $this->setColorTheme($options['colorTheme']);
        }
    }

    /**
     * Get maximum string length
     *
     * @return int
     */
    public function getMaxStrLength(): int
    {
        return $this->maxStrLength;
    }

    /**
     * Set maximum string length
     *
     * @param int $maxStrLength
     * @return self
     */
    public function setMaxStrLength(int $maxStrLength): self
    {
        $this->maxStrLength = max(10, $maxStrLength); // Ensure minimum of 10
        return $this;
    }

    /**
     * Get maximum children
     *
     * @return int
     */
    public function getMaxChildren(): int
    {
        return $this->maxChildren;
    }

    /**
     * Set maximum children
     *
     * @param int $maxChildren
     * @return self
     */
    public function setMaxChildren(int $maxChildren): self
    {
        $this->maxChildren = max(5, $maxChildren); // Ensure minimum of 5
        return $this;
    }

    /**
     * Get maximum depth
     *
     * @return int
     */
    public function getMaxDepth(): int
    {
        return $this->maxDepth;
    }

    /**
     * Set maximum depth
     *
     * @param int $maxDepth
     * @return self
     */
    public function setMaxDepth(int $maxDepth): self
    {
        $this->maxDepth = max(1, $maxDepth); // Ensure minimum of 1
        return $this;
    }

    /**
     * Get whether to show floating button
     *
     * @return bool
     */
    public function getShowFloatingButton(): bool
    {
        return $this->showFloatingButton;
    }

    /**
     * Set whether to show floating button
     *
     * @param bool $showFloatingButton
     * @return self
     */
    public function setShowFloatingButton(bool $showFloatingButton): self
    {
        $this->showFloatingButton = $showFloatingButton;
        return $this;
    }

    /**
     * Get whether arrays and objects are initially expanded
     *
     * @return bool
     */
    public function getInitiallyExpanded(): bool
    {
        return $this->initiallyExpanded;
    }

    /**
     * Set whether arrays and objects are initially expanded
     *
     * @param bool $initiallyExpanded
     * @return self
     */
    public function setInitiallyExpanded(bool $initiallyExpanded): self
    {
        $this->initiallyExpanded = $initiallyExpanded;
        return $this;
    }

    /**
     * Get color theme
     *
     * @return string
     */
    public function getColorTheme(): string
    {
        return $this->colorTheme;
    }

    /**
     * Set color theme
     *
     * @param string $colorTheme
     * @return self
     */
    public function setColorTheme(string $colorTheme): self
    {
        $validThemes = ['dark', 'light', 'monokai', 'solarized', 'github'];

        if (in_array($colorTheme, $validThemes)) {
            $this->colorTheme = $colorTheme;
        }

        return $this;
    }
}
