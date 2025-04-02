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
 * DumperColorizer component for the Catalyst Framework
 *
 */

namespace Catalyst\Helpers\Debug;

/**************************************************************************************
 * DumperColorizer class for handling text coloring in debug output
 *
 * This class is responsible for applying colors to different types of data
 * in the debug output. It supports multiple color themes and can format text
 * for both HTML and CLI output.
 *
 * @package Catalyst\Helpers\Debug;
 */
class DumperColorizer
{
    /**
     * Current color theme
     */
    private string $theme;

    /**
     * Color themes definitions
     */
    private array $themes = [];

    /**
     * Constructor
     *
     * @param string $theme Initial color theme to use
     */
    public function __construct(string $theme = 'dark')
    {
        $this->initializeThemes();
        $this->setTheme($theme);
    }

    /**
     * Initialize available color themes
     *
     * @return void
     */
    private function initializeThemes(): void
    {
        // Dark theme (default)
        $this->themes['dark'] = [
            'string' => ['html' => '#a8ff60', 'cli' => '2;168;255;96'],
            'number' => ['html' => '#ff9d00', 'cli' => '2;255;157;0'],
            'boolean' => ['html' => '#ff628c', 'cli' => '2;255;98;140'],
            'null' => ['html' => '#ff628c', 'cli' => '2;255;98;140'],
            'array' => ['html' => '#54c8ff', 'cli' => '2;84;200;255'],
            'object' => ['html' => '#67d8ef', 'cli' => '2;103;216;239'],
            'resource' => ['html' => '#67d8ef', 'cli' => '2;103;216;239'],
            'key' => ['html' => '#ffcc00', 'cli' => '2;255;204;0'],
            'private' => ['html' => '#ff628c', 'cli' => '2;255;98;140'],
            'protected' => ['html' => '#ffcc00', 'cli' => '2;255;204;0'],
            'public' => ['html' => '#80deea', 'cli' => '2;128;222;234'],
            'meta' => ['html' => '#bbbbbb', 'cli' => '2;187;187;187'],
            'error' => ['html' => '#ff5370', 'cli' => '2;255;83;112'],
            'label' => ['html' => '#80deea', 'cli' => '2;128;222;234'],
            'background' => ['html' => '#1d1e22', 'cli' => ''],
            'text' => ['html' => '#e6e6e6', 'cli' => '2;230;230;230'],
            'header' => ['html' => '#2d2d30', 'cli' => ''],
        ];

        // Light theme
        $this->themes['light'] = [
            'string' => ['html' => '#008000', 'cli' => '2;0;128;0'],
            'number' => ['html' => '#0000ff', 'cli' => '2;0;0;255'],
            'boolean' => ['html' => '#0000ff', 'cli' => '2;0;0;255'],
            'null' => ['html' => '#0000ff', 'cli' => '2;0;0;255'],
            'array' => ['html' => '#800080', 'cli' => '2;128;0;128'],
            'object' => ['html' => '#800080', 'cli' => '2;128;0;128'],
            'resource' => ['html' => '#800080', 'cli' => '2;128;0;128'],
            'key' => ['html' => '#dd4a68', 'cli' => '2;221;74;104'],
            'private' => ['html' => '#ff0000', 'cli' => '2;255;0;0'],
            'protected' => ['html' => '#ff8c00', 'cli' => '2;255;140;0'],
            'public' => ['html' => '#006699', 'cli' => '2;0;102;153'],
            'meta' => ['html' => '#999999', 'cli' => '2;153;153;153'],
            'error' => ['html' => '#ff0000', 'cli' => '2;255;0;0'],
            'label' => ['html' => '#006699', 'cli' => '2;0;102;153'],
            'background' => ['html' => '#ffffff', 'cli' => ''],
            'text' => ['html' => '#333333', 'cli' => '2;51;51;51'],
            'header' => ['html' => '#f5f5f5', 'cli' => ''],
        ];

        // Monokai theme
        $this->themes['monokai'] = [
            'string' => ['html' => '#e6db74', 'cli' => '2;230;219;116'],
            'number' => ['html' => '#ae81ff', 'cli' => '2;174;129;255'],
            'boolean' => ['html' => '#ae81ff', 'cli' => '2;174;129;255'],
            'null' => ['html' => '#ae81ff', 'cli' => '2;174;129;255'],
            'array' => ['html' => '#66d9ef', 'cli' => '2;102;217;239'],
            'object' => ['html' => '#66d9ef', 'cli' => '2;102;217;239'],
            'resource' => ['html' => '#66d9ef', 'cli' => '2;102;217;239'],
            'key' => ['html' => '#f92672', 'cli' => '2;249;38;114'],
            'private' => ['html' => '#f92672', 'cli' => '2;249;38;114'],
            'protected' => ['html' => '#fd971f', 'cli' => '2;253;151;31'],
            'public' => ['html' => '#a6e22e', 'cli' => '2;166;226;46'],
            'meta' => ['html' => '#75715e', 'cli' => '2;117;113;94'],
            'error' => ['html' => '#f92672', 'cli' => '2;249;38;114'],
            'label' => ['html' => '#a6e22e', 'cli' => '2;166;226;46'],
            'background' => ['html' => '#272822', 'cli' => ''],
            'text' => ['html' => '#f8f8f2', 'cli' => '2;248;248;242'],
            'header' => ['html' => '#3e3d32', 'cli' => ''],
        ];

        // Solarized theme
        $this->themes['solarized'] = [
            'string' => ['html' => '#2aa198', 'cli' => '2;42;161;152'],
            'number' => ['html' => '#d33682', 'cli' => '2;211;54;130'],
            'boolean' => ['html' => '#d33682', 'cli' => '2;211;54;130'],
            'null' => ['html' => '#d33682', 'cli' => '2;211;54;130'],
            'array' => ['html' => '#268bd2', 'cli' => '2;38;139;210'],
            'object' => ['html' => '#268bd2', 'cli' => '2;38;139;210'],
            'resource' => ['html' => '#268bd2', 'cli' => '2;38;139;210'],
            'key' => ['html' => '#cb4b16', 'cli' => '2;203;75;22'],
            'private' => ['html' => '#dc322f', 'cli' => '2;220;50;47'],
            'protected' => ['html' => '#cb4b16', 'cli' => '2;203;75;22'],
            'public' => ['html' => '#859900', 'cli' => '2;133;153;0'],
            'meta' => ['html' => '#839496', 'cli' => '2;131;148;150'],
            'error' => ['html' => '#dc322f', 'cli' => '2;220;50;47'],
            'label' => ['html' => '#859900', 'cli' => '2;133;153;0'],
            'background' => ['html' => '#002b36', 'cli' => ''],
            'text' => ['html' => '#93a1a1', 'cli' => '2;147;161;161'],
            'header' => ['html' => '#073642', 'cli' => ''],
        ];

        // GitHub theme
        $this->themes['github'] = [
            'string' => ['html' => '#032f62', 'cli' => '2;3;47;98'],
            'number' => ['html' => '#005cc5', 'cli' => '2;0;92;197'],
            'boolean' => ['html' => '#005cc5', 'cli' => '2;0;92;197'],
            'null' => ['html' => '#005cc5', 'cli' => '2;0;92;197'],
            'array' => ['html' => '#6f42c1', 'cli' => '2;111;66;193'],
            'object' => ['html' => '#6f42c1', 'cli' => '2;111;66;193'],
            'resource' => ['html' => '#6f42c1', 'cli' => '2;111;66;193'],
            'key' => ['html' => '#d73a49', 'cli' => '2;215;58;73'],
            'private' => ['html' => '#d73a49', 'cli' => '2;215;58;73'],
            'protected' => ['html' => '#e36209', 'cli' => '2;227;98;9'],
            'public' => ['html' => '#22863a', 'cli' => '2;34;134;58'],
            'meta' => ['html' => '#6a737d', 'cli' => '2;106;115;125'],
            'error' => ['html' => '#d73a49', 'cli' => '2;215;58;73'],
            'label' => ['html' => '#22863a', 'cli' => '2;34;134;58'],
            'background' => ['html' => '#ffffff', 'cli' => ''],
            'text' => ['html' => '#24292e', 'cli' => '2;36;41;46'],
            'header' => ['html' => '#f6f8fa', 'cli' => ''],
        ];
    }

    /**
     * Set the current color theme
     *
     * @param string $theme Theme name
     * @return self
     */
    public function setTheme(string $theme): self
    {
        if (isset($this->themes[$theme])) {
            $this->theme = $theme;
        } else {
            $this->theme = 'dark'; // Default to dark theme if invalid
        }

        return $this;
    }

    /**
     * Get the current theme name
     *
     * @return string
     */
    public function getTheme(): string
    {
        return $this->theme;
    }

    /**
     * Get all available theme names
     *
     * @return array
     */
    public function getAvailableThemes(): array
    {
        return array_keys($this->themes);
    }

    /**
     * Add a custom color theme
     *
     * @param string $name Theme name
     * @param array $colors Theme color definitions
     * @return self
     */
    public function addTheme(string $name, array $colors): self
    {
        // Ensure all required color types are defined
        $requiredColors = [
            'string', 'number', 'boolean', 'null', 'array', 'object',
            'resource', 'key', 'private', 'protected', 'public',
            'meta', 'error', 'label', 'background', 'text', 'header'
        ];

        $valid = true;
        foreach ($requiredColors as $colorType) {
            if (!isset($colors[$colorType]) ||
                !isset($colors[$colorType]['html']) ||
                !isset($colors[$colorType]['cli'])) {
                $valid = false;
                break;
            }
        }

        if ($valid) {
            $this->themes[$name] = $colors;
        }

        return $this;
    }

    /**
     * Get the color for a specific type in the current theme
     *
     * @param string $type Color type (string, number, boolean, etc.)
     * @param bool $isHtml Whether to return HTML or CLI color
     * @return string Color value
     */
    public function getColor(string $type, bool $isHtml): string
    {
        if (!isset($this->themes[$this->theme][$type])) {
            return $isHtml ? '#ffffff' : '2;255;255;255'; // Default to white if type not found
        }

        return $this->themes[$this->theme][$type][$isHtml ? 'html' : 'cli'];
    }

    /**
     * Get background color for the current theme
     *
     * @return string HTML color code
     */
    public function getBackgroundColor(): string
    {
        return $this->themes[$this->theme]['background']['html'];
    }

    /**
     * Get text color for the current theme
     *
     * @return string HTML color code
     */
    public function getTextColor(): string
    {
        return $this->themes[$this->theme]['text']['html'];
    }

    /**
     * Get header background color for the current theme
     *
     * @return string HTML color code
     */
    public function getHeaderColor(): string
    {
        return $this->themes[$this->theme]['header']['html'];
    }

    /**
     * Apply color to text based on type
     *
     * @param string $text Text to colorize
     * @param string $type Color type (string, number, boolean, etc.)
     * @param bool $isHtml Whether to format for HTML or CLI
     * @return string Colorized text
     */
    public function colorize(string $text, string $type, bool $isHtml): string
    {
        $color = $this->getColor($type, $isHtml);

        if ($isHtml) {
            return '<span style="color:' . $color . ';">' . $text . '</span>';
        } else {
            // CLI coloring using ANSI escape sequences
            return "\033[38;" . $color . "m" . $text . "\033[0m";
        }
    }

    /**
     * Get the type color based on variable type
     *
     * @param string $type Variable type
     * @param bool $isHtml Whether to format for HTML
     * @return string Type name for colorizing
     */
    public function getTypeColor(string $type, bool $isHtml): string
    {
        return match ($type) {
            'string' => 'string',
            'integer', 'double' => 'number',
            'boolean' => 'boolean',
            'NULL' => 'null',
            'array' => 'array',
            'object' => 'object',
            'resource' => 'resource',
            default => 'meta'
        };
    }
}
