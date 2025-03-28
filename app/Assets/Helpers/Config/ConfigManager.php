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

namespace Catalyst\Helpers\Config;

use Catalyst\Framework\Traits\SingletonTrait;

/**************************************************************************************
 * ConfigManager - Configuration manager class
 *
 * Loads and provides access to application configuration from JSON files
 *
 * @package Catalyst\Helpers\Config;
 */
class ConfigManager
{
    use SingletonTrait;

    /**
     * @var array Loaded configuration data
     */
    private array $config = [];

    /**
     * @var string Current environment (development/production)
     */
    private string $environment;

    /**
     * @var string Path to configuration directory
     */
    private string $configPath;

    /**
     * @var bool Whether the application is configured
     */
    private bool $isConfigured = false;

    /**
     * ConfigManager constructor
     */
    public function __construct()
    {
        // Default to development if not specified
        $this->environment = defined('GET_ENVIRONMENT') ? GET_ENVIRONMENT : 'development';

        // Set config path
        $this->configPath = implode(DS, [PD, 'bootstrap', 'config', $this->environment]);

        // Load configuration
        $this->loadConfig();
    }

    /**
     * Load all configuration files
     *
     * @return void
     */
    private function loadConfig(): void
    {
        // Check if the environment directory exists
        if (!is_dir($this->configPath)) {
            return;
        }

        // Load configuration files
        $this->loadConfigFiles();

        // Determine if app is configured
        $this->determineIfConfigured();
    }

    /**
     * Load all configuration files from the environment directory
     *
     * @return void
     */
    private function loadConfigFiles(): void
    {
        // Get all JSON files in the directory
        $files = glob($this->configPath . DS . '*.json');

        if (empty($files)) {
            return;
        }

        foreach ($files as $file) {
            $section = pathinfo($file, PATHINFO_FILENAME);
            $content = file_get_contents($file);
            $data = json_decode($content, true);

            if (is_array($data)) {
                $this->config[$section] = $data;
            }
        }
    }

    /**
     * Determine if the application is configured
     *
     * @return void
     */
    private function determineIfConfigured(): void
    {
        // Application is considered configured if there's at least
        // app configuration with minimum required settings
        if (isset($this->config['app']) &&
            isset($this->config['app']['company']) &&
            isset($this->config['app']['project'])) {
            $this->isConfigured = true;
        }
    }

    /**
     * Get configuration value by key
     *
     * @param string $key Dot notation key (e.g. 'app.company.company_name')
     * @param mixed $default Default value if key not found
     * @return mixed Configuration value or default
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $data = $this->config;

        foreach ($segments as $segment) {
            if (!isset($data[$segment])) {
                return $default;
            }
            $data = $data[$segment];
        }

        return $data;
    }

    /**
     * Check if a configuration key exists
     *
     * @param string $key Dot notation key
     * @return bool True if key exists
     */
    public function has(string $key): bool
    {
        $segments = explode('.', $key);
        $data = $this->config;

        foreach ($segments as $segment) {
            if (!isset($data[$segment])) {
                return false;
            }
            $data = $data[$segment];
        }

        return true;
    }

    /**
     * Get all configuration data
     *
     * @return array All configuration data
     */
    public function all(): array
    {
        return $this->config;
    }

    /**
     * Get a specific configuration section
     *
     * @param string $section Section name
     * @return array|null Section data or null if section doesn't exist
     */
    public function section(string $section): ?array
    {
        return $this->config[$section] ?? null;
    }

    /**
     * Check if the application is configured
     *
     * @return bool True if configured
     */
    public function isConfigured(): bool
    {
        return $this->isConfigured;
    }

    /**
     * Get current environment
     *
     * @return string Current environment
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }
}