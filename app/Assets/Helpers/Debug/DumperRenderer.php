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
 * DumperRenderer component for the Catalyst Framework
 *
 */

namespace Catalyst\Helpers\Debug;

/**************************************************************************************
 * DumperRenderer class for rendering debug output
 *
 * This class is responsible for rendering the debug output in different formats,
 * such as HTML with modal or CLI. It handles the visual presentation of the data.
 *
 * @package Catalyst\Helpers\Debug;
 */
class DumperRenderer
{
    /**
     * DumperConfig instance
     */
    private DumperConfig $config;

    /**
     * DumperColorizer instance
     */
    private DumperColorizer $colorizer;

    /**
     * DumperCollapsible instance
     */
    private DumperCollapsible $collapsible;

    /**
     * Counter for generating unique IDs for dump modals
     */
    private static int $dumpCounter = 0;

    /**
     * Constructor
     *
     * @param DumperConfig $config Configuration instance
     * @param DumperColorizer $colorizer Colorizer instance
     * @param DumperCollapsible $collapsible Collapsible instance
     */
    public function __construct(
        DumperConfig      $config,
        DumperColorizer   $colorizer,
        DumperCollapsible $collapsible
    )
    {
        $this->config = $config;
        $this->colorizer = $colorizer;
        $this->collapsible = $collapsible;
    }

    /**
     * Render debug output
     *
     * @param array $data Array of variables to dump
     * @param array|null $caller Caller information (file, line)
     * @param bool $isHtml Whether to render as HTML
     * @return string Rendered output
     */
    public function render(array $data, ?array $caller, bool $isHtml): string
    {
        if (empty($data)) {
            return '';
        }

        if (!$isHtml) {
            return $this->renderCli($data, $caller);
        }

        return $this->renderHtml($data, $caller);
    }

    /**
     * Render debug output for CLI
     *
     * @param array $data Array of variables to dump
     * @param array|null $caller Caller information (file, line)
     * @return string Rendered CLI output
     */
    private function renderCli(array $data, ?array $caller): string
    {
        $output = '';
        // Get terminal width or default to 80
        $terminalWidth = 80;
        if (defined('TW')) {
            $terminalWidth = TW;
        } elseif (function_exists('exec')) {
            @exec('tput cols 2>/dev/null', $columns);
            if (!empty($columns[0])) {
                $terminalWidth = (int)$columns[0];
            }
        }
        $width = min(120, $terminalWidth);

        // Display caller information if available
        if ($caller) {
            $callerText = "Called from: " . $caller['file'] . " (line " . $caller['line'] . ")";
            $output .= str_repeat('=', $width) . PHP_EOL;
            $output .= "\033[1;36m" . $callerText . "\033[0m" . PHP_EOL;
            $output .= str_repeat('=', $width) . PHP_EOL;
        }

        // Reset collapse counter for each dump call
        $this->collapsible->resetCounter();

        foreach ($data as $var) {
            $output .= $var . PHP_EOL;
            $output .= str_repeat('-', $width) . PHP_EOL;
        }

        return $output;
    }

    /**
     * Render debug output for HTML
     *
     * @param array $data Array of variables to dump
     * @param array|null $caller Caller information (file, line)
     * @return string Rendered HTML output
     */
    private function renderHtml(array $data, ?array $caller): string
    {
        // Create a modal
        $dumpId = 'catalyst-dump-' . (++self::$dumpCounter);
        $modalId = $dumpId . '-modal';
        $btnId = $dumpId . '-btn';

        $output = $this->generateCss($dumpId);
        $output .= $this->generateJavaScript($dumpId, $modalId);
        $output .= $this->generateModal($dumpId, $modalId, $data, $caller);

        if ($this->config->getShowFloatingButton()) {
            $output .= $this->generateFloatingButton($dumpId, $btnId, $data);
        }

        return $output;
    }

    /**
     * Generate CSS for HTML output
     *
     * @param string $dumpId Unique ID for this dump
     * @return string CSS code
     */
    private function generateCss(string $dumpId): string
    {
        $bgColor = $this->colorizer->getBackgroundColor();
        $textColor = $this->colorizer->getTextColor();
        $headerColor = $this->colorizer->getHeaderColor();

        return '<style>
            .' . $dumpId . '-modal {
                display: none;
                position: fixed;
                z-index: 9999;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: hidden;
                background-color: rgba(0, 0, 0, 0.7);
                backdrop-filter: blur(2px);
            }
            .' . $dumpId . '-modal-content {
                position: relative;
                background-color: ' . $bgColor . ';
                margin: 30px auto;
                padding: 0;
                width: 90%;
                max-width: 1200px;
                max-height: 90vh;
                border-radius: 8px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
                display: flex;
                flex-direction: column;
                overflow: hidden;
            }
            .' . $dumpId . '-close {
                position: absolute;
                top: 50%;
                right: 15px;
                transform: translateY(-50%);
                width: 14px;
                height: 14px;
                background-color: #ff5f57;
                border-radius: 50%;
                cursor: pointer;
                z-index: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.1);
            }
            .' . $dumpId . '-close:hover::before {
                content: "×";
                font-size: 12px;
                color: rgba(0, 0, 0, 0.5);
                line-height: 1;
            }
            .' . $dumpId . '-btn {
                position: fixed;
                bottom: 20px;
                left: 20px;
                background-color: ' . $headerColor . ';
                color: ' . $this->colorizer->getColor('label', true) . ';
                border: none;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                font-size: 24px;
                cursor: pointer;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
                z-index: 9998;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s ease;
            }
            .' . $dumpId . '-btn:hover {
                background-color: ' . $this->adjustBrightness($headerColor, 20) . ';
                transform: scale(1.05);
            }
            .' . $dumpId . '-btn-badge {
                position: absolute;
                top: -5px;
                right: -5px;
                background-color: #ef5350;
                color: white;
                border-radius: 50%;
                width: 20px;
                height: 20px;
                font-size: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .' . $dumpId . '-modal-header {
                padding: 15px 40px 15px 15px;
                background-color: ' . $headerColor . ';
                border-bottom: 1px solid ' . $this->adjustBrightness($headerColor, 20) . ';
                border-radius: 8px 8px 0 0;
                flex-shrink: 0;
                position: relative;
            }
            .' . $dumpId . '-modal-body {
                padding: 15px;
                overflow: auto;
                max-height: calc(90vh - 60px);
                /*overflow-x: auto;*/
            }
            /* Ensure proper indentation display */
            .' . $dumpId . '-modal-body pre {
                white-space: pre;
                word-wrap: normal;
                tab-size: 4;
            }
            /* Preserve indentation in collapsible content */
            #content-* {
                white-space: pre !important;
            }
        </style>';
    }

    /**
     * Generate JavaScript for HTML output
     *
     * @param string $dumpId Unique ID for this dump
     * @param string $modalId Modal ID
     * @return string JavaScript code
     */
    private function generateJavaScript(string $dumpId, string $modalId): string
    {
        return '<script>
            // Function to toggle collapsible sections
            ' . $this->collapsible->getJavaScript() . '
            
            // Function to toggle modal visibility
            function toggleModal' . self::$dumpCounter . '() {
                const modal = document.getElementById("' . $modalId . '");
                if (modal.style.display === "block") {
                    modal.style.display = "none";
                } else {
                    modal.style.display = "block";
                }
            }
            
            // Close modal when clicking outside of it
            document.addEventListener("DOMContentLoaded", function() {
                const modal = document.getElementById("' . $modalId . '");
                window.addEventListener("click", function(event) {
                    if (event.target === modal) {
                        modal.style.display = "none";
                    }
                });
            });
        </script>';
    }

    /**
     * Generate modal HTML
     *
     * @param string $dumpId Unique ID for this dump
     * @param string $modalId Modal ID
     * @param array $data Array of formatted variables
     * @param array|null $caller Caller information
     * @return string Modal HTML
     */
    private function generateModal(string $dumpId, string $modalId, array $data, ?array $caller): string
    {
        $output = '<div id="' . $modalId . '" class="' . $dumpId . '-modal">
            <div class="' . $dumpId . '-modal-content">
                <div class="' . $dumpId . '-modal-header">
                    <span class="' . $dumpId . '-close" onclick="toggleModal' . self::$dumpCounter . '()"></span>';

        // Display caller information in modal header if available
        if ($caller) {
            $callerText = "Called from: " . $caller['file'] . " (line " . $caller['line'] . ")";
            $output .= '<span style="color:' . $this->colorizer->getColor('label', true) . ';font-weight:bold;">' .
                htmlspecialchars($callerText) . '</span>';
        } else {
            $output .= '<span style="color:' . $this->colorizer->getColor('label', true) . ';font-weight:bold;">Debug Information</span>';
        }

        $output .= '</div>
                <div class="' . $dumpId . '-modal-body">
                    <pre style="background-color:' . $this->colorizer->getBackgroundColor() . '; color:' .
            $this->colorizer->getTextColor() . '; padding:0; margin:0; font-family:monospace; white-space:pre; tab-size:4;">';

        foreach ($data as $var) {
            $output .= $var . PHP_EOL;
            $output .= '<hr style="border:1px dashed #505050; margin:10px 0;">';
        }

        $output .= '</pre>
                </div>
            </div>
        </div>';

        return $output;
    }

    /**
     * Generate floating button HTML
     *
     * @param string $dumpId Unique ID for this dump
     * @param string $btnId Button ID
     * @param array $data Array of variables
     * @return string Button HTML
     */
    private function generateFloatingButton(string $dumpId, string $btnId, array $data): string
    {
        return '<button id="' . $btnId . '" class="' . $dumpId . '-btn" onclick="toggleModal' . self::$dumpCounter . '()" title="Show Debug Information">
            <span style="font-size:20px;">&#128270;</span>
            <span class="' . $dumpId . '-btn-badge">' . count($data) . '</span>
        </button>';
    }

    /**
     * Adjust brightness of a hex color
     *
     * @param string $hexColor Hex color code
     * @param int $percent Percentage to adjust (-100 to 100)
     * @return string Adjusted hex color
     */
    private function adjustBrightness(string $hexColor, int $percent): string
    {
        // Remove # if present
        $hex = ltrim($hexColor, '#');

        // Convert to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Adjust brightness
        $r = max(0, min(255, $r + $percent));
        $g = max(0, min(255, $g + $percent));
        $b = max(0, min(255, $b + $percent));

        // Convert back to hex
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
}
