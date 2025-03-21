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

namespace Catalyst\Framework\Traits;

/**************************************************************************************
 * Trait that handles: Singleton Instance
 *
 * @package Catalyst\Framework\Traits;
 */
trait SingletonTrait
{
    /**
     * @var self|null
     */
    private static self|null $instance;

    /**
     * @var array
     */
    private static array $arguments = [];

    /**
     * @var bool Enable/disable testing mode
     */
    private static bool $testingMode = false;

    /**
     * @param mixed ...$args
     * @return self
     */
    public static function getInstance(mixed ...$args): self
    {

        if (self::$testingMode) {
            return self::getTestInstance(...$args);
        }

        if (!isset(self::$instance)) {
            self::$arguments = $args;
            /** @var mixed $args */
            self::$instance = !empty($args) ? new self(...$args) : new self();
        }

        return self::$instance;
    }

    /**
     * Get instance for testing
     *
     * @param mixed ...$args Constructor arguments
     * @return self
     */
    private static function getTestInstance(mixed ...$args): self
    {
        self::$arguments = $args;
        return !empty($args) ? new self(...$args) : new self();
    }

    /**
     * Enable testing mode - each getInstance() call returns a new instance
     *
     * @param bool $enabled Whether testing mode is enabled
     * @return void
     */
    public static function enableTestingMode(bool $enabled = true): void
    {
        self::$testingMode = $enabled;
    }

    /**
     * Reset the singleton instance
     *
     * @return void
     */
    public static function resetInstance(): void
    {
        self::$instance = null;
        self::$arguments = [];
    }

    /**
     * Set a specific instance (for mocking/testing)
     *
     * @param self $instance The instance to use
     * @return void
     */
    public static function setInstance(self $instance): void
    {
        self::$instance = $instance;
    }

    /**
     * Get constructor arguments
     *
     * @return array
     */
    protected static function getArguments(): array
    {
        return self::$arguments;
    }
}