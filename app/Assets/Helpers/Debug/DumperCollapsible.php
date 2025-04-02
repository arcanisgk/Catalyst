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
 * DumperCollapsible component for the Catalyst Framework
 *
 */

namespace Catalyst\Helpers\Debug;

/**************************************************************************************
 * DumperCollapsible class for handling collapsible sections in debug output
 *
 * This class is responsible for creating collapsible sections in the debug output,
 * allowing users to expand and collapse complex data structures for better readability.
 *
 * @package Catalyst\Helpers\Debug;
 */
class DumperCollapsible
{
    /**
     * Counter for generating unique IDs for collapsible elements
     */
    private int $collapseCounter = 0;

    /**
     * DumperColorizer instance for text coloring
     */
    private DumperColorizer $colorizer;

    /**
     * Constructor
     *
     * @param DumperColorizer $colorizer Colorizer instance for text coloring
     */
    public function __construct(DumperColorizer $colorizer)
    {
        $this->colorizer = $colorizer;
    }

    /**
     * Reset the collapse counter
     *
     * @return void
     */
    public function resetCounter(): void
    {
        $this->collapseCounter = 0;
    }

    /**
     * Create a collapsible section with chevron toggle
     *
     * @param string $header Header content
     * @param string $content Content to be collapsed/expanded
     * @param bool $isHtml Whether to format for HTML output
     * @param bool $initiallyExpanded Whether the content should be initially expanded
     * @param int $depth Current nesting depth for indentation
     * @return string Formatted output with collapsible functionality
     */
    public function create(
        string $header,
        string $content,
        bool   $isHtml,
        bool   $initiallyExpanded = true,
        int    $depth = 0
    ): string
    {
        $indent = str_repeat('    ', $depth);

        if (!$isHtml) {
            // For CLI, just return the content without collapsible functionality
            // Make sure there's no extra newline before the closing brace
            return $indent . $header . " {" . PHP_EOL . rtrim($content) . PHP_EOL . $indent . "}";
        }

        // Generate a unique ID for this collapsible section
        $id = ++$this->collapseCounter;

        // Determine initial state
        $displayStyle = $initiallyExpanded ? 'block' : 'none';
        $chevronChar = $initiallyExpanded ? '&#9660;' : '&#9658;';
        $chevronTitle = $initiallyExpanded ? 'Collapse' : 'Expand';

        // Create the collapsible HTML structure
        $result = $indent . '<span style="cursor:pointer;" onclick="toggleCollapse(' . $id . ')">';
        $result .= '<span id="chevron-' . $id . '" title="' . $chevronTitle . '" style="display:inline-block;width:15px;text-align:center;color:#9e9e9e;">' . $chevronChar . '</span>';
        $result .= $header . ' {</span>' . PHP_EOL;

        // Ensure proper indentation of content by wrapping it in a div with preserved whitespace
        // Remove any trailing newlines to prevent extra spacing before the closing brace
        $trimmedContent = rtrim($content);

        if ($isHtml) {
            $result .= '<div id="content-' . $id . '" style="display:' . $displayStyle . ';white-space:pre;">' . $trimmedContent . '</div>';
        } else {
            $result .= '<div id="content-' . $id . '" style="display:' . $displayStyle . ';">' . $trimmedContent . '</div>';
        }

        $result .= $indent . '}';

        return $result;
    }

    /**
     * Generate JavaScript code for collapsible functionality
     *
     * @return string JavaScript code
     */
    public function getJavaScript(): string
    {
        return '
        function toggleCollapse(id) {
            const content = document.getElementById("content-" + id);
            const chevron = document.getElementById("chevron-" + id);
            
            if (content.style.display === "none") {
                content.style.display = "block";
                chevron.innerHTML = "&#9660;"; // Down chevron
                chevron.title = "Collapse";
            } else {
                content.style.display = "none";
                chevron.innerHTML = "&#9658;"; // Right chevron
                chevron.title = "Expand";
            }
        }';
    }
}
