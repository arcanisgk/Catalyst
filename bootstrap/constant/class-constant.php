<?php

declare(strict_types=1);

/**************************************************************************************
 *
 * Catalyst PHP Framework
 * PHP Version 8.3 (Required).
 *
 * @package   Catalyst
 * @subpackage Public
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
 */


if (!defined('LOADED_CLASS_CONSTANT')) {
    define('LOADED_CLASS_CONSTANT', true);

    /**************************************************************************************
     * Reads and processes environment variables from a .env file.
     *
     * This function attempts to load environment variables from a .env file.
     * If the .env file does not exist, it tries to copy from a .env.example file.
     * It defines constants for each environment variable found in the file.
     *
     * @return array
     * @throws Exception
     */
    function readEnvironmentVariable(): array
    {

        $envPath = implode(DS, [PD, '.env']);
        $envArray = [];
        try {
            if (!file_exists($envPath)) {
                $examplePath = implode(DS, [PD, '.env.example']);
                if (file_exists($examplePath)) {
                    if (!copy($examplePath, $envPath)) {
                        throw new Exception("Unable to copy from example file $envPath");
                    }
                } else {
                    throw new Exception("File Missing: $examplePath");
                }
            }

            $content = file_get_contents($envPath);
            if ($content === false) {
                throw new Exception("Unable to read file: $envPath");
            }

            $lines = explode("\n", $content);
            if (empty($lines)) {
                throw new Exception(".env file is empty");
            }

            foreach ($lines as $line) {
                if (str_starts_with(trim($line), '#')) {
                    continue;
                }

                if (str_contains($line, '=')) {
                    list($name, $value) = explode('=', $line, 2);
                    $name = trim($name);
                    $value = trim($value);

                    if (strlen($value) > 1 && (
                            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                            (str_starts_with($value, "'") && str_ends_with($value, "'"))
                        )) {
                        $value = substr($value, 1, -1);
                    }

                    if (str_contains($value, ',')) {
                        $value = array_map('trim', explode(',', $value));
                    }

                    if (!isset($envArray[$name])) {
                        $envArray[$name] = $value;
                    }
                }
            }

        } catch (Exception $e) {
            echo $e->getMessage();

            // Define ENV as false to indicate failure
            if (!defined('ENV')) {
                define('ENV', false);
            }

            // Only exit in CLI mode or if explicitly configured
            exit(1);
        }

        if (!defined('ENV')) {
            define('ENV', true);
        }

        return $envArray;
    }

    $envArray = [];

    try {
        $envArray = readEnvironmentVariable();
    } catch (Exception $e) {
        echo "Environment Error: " . $e->getMessage();
    }

    if (!defined('IS_DEVELOPMENT')) {
        /**
         * Defines if the application is running in development mode
         */
        define('IS_DEVELOPMENT', isset($envArray['APP_ENV']) && $envArray['APP_ENV'] === 'development');
    }

    if (!defined('IS_PRODUCTION')) {
        /**
         * Defines if the application is running in production mode
         */
        define('IS_PRODUCTION', isset($envArray['APP_ENV']) && $envArray['APP_ENV'] === 'production');
    }

    if (!defined('GET_ENVIRONMENT')) {
        /**
         * The current environment name
         */
        define('GET_ENVIRONMENT', $envArray['APP_ENV'] ?? 'unknown');
    }

    if (!defined('CATALYST_VERSION')) {
        /**
         * Framework version
         */
        define('CATALYST_VERSION', $envArray['APP_VERSION'] ?? 'unknown');
    }

    if (!defined('CATALYST_KEY')) {
        /**
         * Application security key
         */
        define('CATALYST_KEY', $envArray['APP_KEY'] ?? 'unknown');
    }

    if (!defined('APP_NAME')) {
        /**
         * Application name
         */
        define('APP_NAME', $envArray['APP_NAME'] ?? 'Catalyst Framework');
    }

    if (!defined('APP_VERSION')) {
        /**
         * Application version
         */
        define('APP_VERSION', $envArray['APP_VERSION'] ?? '1.0.0');
    }

    if (!defined('APP_URL')) {
        /**
         * Application base URL
         */
        define('APP_URL', $envArray['APP_URL'] ?? 'https://localhost');
    }
    if (!defined('LOG_LEVEL')) {
        /**
         * Log Level - based on environment
         */
        define('LOG_LEVEL', defined('IS_DEVELOPMENT') && IS_DEVELOPMENT ? 'DEBUG' : 'ERROR');
    }
}