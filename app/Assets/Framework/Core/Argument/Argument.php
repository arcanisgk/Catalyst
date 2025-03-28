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

namespace Catalyst\Framework\Core\Argument;

use Catalyst\Framework\Traits\SingletonTrait;


/**************************************************************************************
 * Class that handles Argument for command line.
 *
 * @package Catalyst\Framework\Core\Argument
 */
class Argument
{

    use SingletonTrait;

    /**
     * @var string|null The command name (e.g., 'mail:dkim')
     */
    protected ?string $command = null;

    /**
     * @var array Options parsed from command line (e.g., ['domain' => 'example.com'])
     */
    protected array $options = [];

    /**
     * @var array<int,string> Positional arguments
     */
    private array $positionalArguments = [];


    /**
     * Constructor - parses arguments from $_SERVER['argv']
     */
    public function __construct()
    {
        $this->parseArguments();
    }

    /**
     * Parse command line arguments
     *
     * @return void
     */
    protected function parseArguments(): void
    {
        global $argv;

        // Skip if not running from CLI
        if (!isset($argv)) {
            return;
        }

        // First argument is always the script name
        array_shift($argv);

        // Next argument could be the command
        if (!empty($argv) && !str_starts_with($argv[0], '-')) {
            $this->command = array_shift($argv);
        }

        // Process remaining arguments
        $currentIndex = 0;
        foreach ($argv as $arg) {
            // Handle --option=value format
            if (str_starts_with($arg, '--')) {
                $option = substr($arg, 2); // Remove leading --

                if (str_contains($option, '=')) {
                    list($name, $value) = explode('=', $option, 2);
                    $this->options[$name] = $value;
                } else {
                    // Handle --flag format (boolean true)
                    $this->options[$option] = true;
                }
            } // Handle -o value format
            elseif (str_starts_with($arg, '-') && strlen($arg) == 2) {
                $name = substr($arg, 1);

                // Check if next argument exists and isn't an option
                if (isset($argv[$currentIndex + 1]) && !str_starts_with($argv[$currentIndex + 1], '-')) {
                    $this->options[$name] = $argv[$currentIndex + 1];
                    // Skip the next iteration as we've used this value
                    $currentIndex++;
                } else {
                    // Flag with no value
                    $this->options[$name] = true;
                }
            } // Positional argument
            elseif (!str_starts_with($arg, '-')) {
                $this->positionalArguments[] = $arg;
            }

            $currentIndex++;
        }
    }

    /**
     * Get the command name
     *
     * @return string|null Command name
     */
    public function getCommand(): ?string
    {
        return $this->command;
    }

    /**
     * Get an option value
     *
     * @param string $name Option name
     * @param mixed|null $default Default value if option not set
     * @return mixed Option value or default
     */
    public function getOption(string $name, mixed $default = null): mixed
    {
        return $this->options[$name] ?? $default;
    }

    /**
     * Check if an option exists
     *
     * @param string $name Option name
     * @return bool True if option exists
     */
    public function hasOption(string $name): bool
    {
        return isset($this->options[$name]);
    }

    /**
     * Get all options
     *
     * @return array All options
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get a positional argument by index
     *
     * @param int $index Argument index (0-based)
     * @param mixed|null $default Default value if argument doesn't exist
     * @return mixed Argument value or default
     */
    public function getArgument(int $index, mixed $default = null): mixed
    {
        return $this->positionalArguments[$index] ?? $default;
    }

    /**
     * Get all positional arguments
     *
     * @return array Positional arguments
     */
    public function getArguments(): array
    {
        return $this->positionalArguments;
    }

    /**
     * Check if help is requested
     *
     * @return bool True if help option is set
     */
    public function isHelpRequested(): bool
    {
        return $this->hasOption('help') || $this->hasOption('h');
    }
}

if (isset($argv)) {
    Argument::getInstance($argv);
}