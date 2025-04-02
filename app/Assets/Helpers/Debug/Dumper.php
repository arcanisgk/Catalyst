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
 * Dumper component for the Catalyst Framework
 *
 */

namespace Catalyst\Helpers\Debug;

use Catalyst\Framework\Traits\SingletonTrait;

/**************************************************************************************
 * Dumper class for debugging variables
 *
 * This class serves as the main entry point for the debugging system.
 * It coordinates the process of dumping variables for inspection.
 *
 * @package Catalyst\Helpers\Debug;
 */
class Dumper
{
    use SingletonTrait;

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
     * DumperFormatter instance
     */
    private DumperFormatter $formatter;

    /**
     * DumperRenderer instance
     */
    private DumperRenderer $renderer;

    /**
     * Initialize the Dumper instance
     *
     * @return void
     */
    protected function initialize(): void
    {
        $this->config = new DumperConfig();
        $this->colorizer = new DumperColorizer($this->config->getColorTheme());
        $this->collapsible = new DumperCollapsible($this->colorizer);
        $this->formatter = new DumperFormatter($this->config, $this->colorizer, $this->collapsible);
        $this->renderer = new DumperRenderer($this->config, $this->colorizer, $this->collapsible);
    }

    /**
     * Dump variables with formatting
     *
     * @param array $options Options array with 'data' containing variables to dump
     * @return void
     */
    public static function dump(array $options): void
    {
        $instance = self::getInstance();

        // Ensure initialization has occurred
        if (!isset($instance->config)) {
            $instance->initialize();
        }

        $data = $options['data'] ?? [];
        $caller = $options['caller'] ?? null;
        $config = $options['config'] ?? [];

        if (empty($data)) {
            return;
        }

        // Apply any custom configuration
        if (!empty($config)) {
            $instance->config->applyOptions($config);
        }

        // Set color theme if specified
        if (isset($config['colorTheme'])) {
            $instance->colorizer->setTheme($config['colorTheme']);
        }

        // Determine if we're in CLI mode
        $isHtml = !(defined('IS_CLI') && IS_CLI) && PHP_SAPI !== 'cli';

        // Reset collapse counter for each dump call
        $instance->collapsible->resetCounter();

        // Format each variable
        $formattedData = [];
        foreach ($data as $var) {
            $formattedData[] = $instance->formatter->formatVar($var, 'Output', $isHtml);
        }

        // Render the output
        echo $instance->renderer->render($formattedData, $caller, $isHtml);
    }

    /**
     * Configure the dumper
     *
     * @param array $options Configuration options
     * @return void
     */
    public static function configure(array $options): void
    {
        $instance = self::getInstance();

        // Ensure initialization has occurred
        if (!isset($instance->config)) {
            $instance->initialize();
        }

        $instance->config->applyOptions($options);

        if (isset($options['colorTheme'])) {
            $instance->colorizer->setTheme($options['colorTheme']);
        }
    }

    /**
     * Get available color themes
     *
     * @return array List of available theme names
     */
    public static function getAvailableThemes(): array
    {
        $instance = self::getInstance();

        // Ensure initialization has occurred
        if (!isset($instance->colorizer)) {
            $instance->initialize();
        }

        return $instance->colorizer->getAvailableThemes();
    }

    /**
     * Add a custom color theme
     *
     * @param string $name Theme name
     * @param array $colors Theme color definitions
     * @return void
     */
    public static function addTheme(string $name, array $colors): void
    {
        $instance = self::getInstance();

        // Ensure initialization has occurred
        if (!isset($instance->colorizer)) {
            $instance->initialize();
        }

        $instance->colorizer->addTheme($name, $colors);
    }

    /**
     * Set the current color theme
     *
     * @param string $theme Theme name
     * @return void
     */
    public static function setTheme(string $theme): void
    {
        $instance = self::getInstance();

        // Ensure initialization has occurred
        if (!isset($instance->colorizer) || !isset($instance->config)) {
            $instance->initialize();
        }

        $instance->colorizer->setTheme($theme);
        $instance->config->setColorTheme($theme);
    }

    /**
     * Get the current color theme
     *
     * @return string Theme name
     */
    public static function getTheme(): string
    {
        $instance = self::getInstance();

        // Ensure initialization has occurred
        if (!isset($instance->colorizer)) {
            $instance->initialize();
        }

        return $instance->colorizer->getTheme();
    }
}