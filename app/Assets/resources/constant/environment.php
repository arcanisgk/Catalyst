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

use App\Assets\Framework\Exceptions\FileSystemException;
use App\Assets\Helpers\Log\Logger;

/**************************************************************************************
 * Reads and processes environment variables from a .env file.
 *
 * This function attempts to load environment variables from a .env file.
 * If the .env file does not exist, it tries to copy from a .env.example file.
 * It defines constants for each environment variable found in the file.
 *
 * @return void
 * @throws Exception
 */
function readEnvironmentVariable(): void
{
    $envPath = PD . DS . '.env';

    try {
        if (!file_exists($envPath)) {
            $examplePath = PD . DS . '.env.example';
            if (file_exists($examplePath)) {
                if (!copy($examplePath, $envPath)) {
                    throw FileSystemException::unableToWriteFile($envPath, "Unable to copy from example file");
                }
            } else {
                throw FileSystemException::fileMissing($examplePath);
            }
        }

        $content = file_get_contents($envPath);
        if ($content === false) {
            throw FileSystemException::unableToReadFile($envPath);
        }

        $lines = explode("\n", $content);
        if (empty($lines)) {
            throw new RuntimeException(".env file is empty");
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

                if (!defined($name)) {
                    define($name, $value);
                }
            }
        }

    } catch (Exception $e) {
        // Log the error if Logger is available
        if (class_exists('\\App\\Assets\\Helpers\\Log\\Logger')) {
            Logger::getInstance()->critical(
                "Environment configuration error: " . $e->getMessage(),
                ['exception' => get_class($e)]
            );
        }

        // In development, show error details
        if (defined('IS_DEVELOPMENT') && IS_DEVELOPMENT) {
            echo "Environment Error: " . $e->getMessage();
        }

        // Define ENV as false to indicate failure
        if (!defined('ENV')) {
            define('ENV', false);
        }

        // Only exit in CLI mode or if explicitly configured
        if (PHP_SAPI === 'cli' || (defined('STRICT_ENV_CHECKING') && STRICT_ENV_CHECKING)) {
            exit(1);
        }
    }

    if (!defined('ENV')) {
        define('ENV', true);
    }
}

readEnvironmentVariable();

if (!defined('IS_DEVELOPMENT')) {
    /**
     * Defines a constant to indicate whether the application is running in development mode
     *
     * Checks if the APP_ENV environment variable is set to 'development'
     * and creates a boolean constant 'IS_DEVELOPMENT' accordingly
     */
    define('IS_DEVELOPMENT', defined('APP_ENV') && APP_ENV === 'development');
}


if (!defined('IS_PRODUCTION')) {
    /**
     * Defines a constant to indicate whether the application is running in production mode
     *
     * Checks if the APP_ENV environment variable is set to 'production'
     * and creates a boolean constant 'IS_PRODUCTION' accordingly
     */
    define('IS_PRODUCTION', defined('APP_ENV') && APP_ENV === 'production');
}

