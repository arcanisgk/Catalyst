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

namespace Catalyst\Framework\Core;

use Catalyst\Framework\Traits\SingletonTrait;


/**************************************************************************************
 * Class that handles Argument for command line.
 *
 * @package Catalyst\Framework\Core
 */
class Argument
{

    use SingletonTrait;

    /**
     * @var array
     */
    private static array $arguments = [];

    /**
     * Constructor that receives command line arguments
     *
     * @param array $args Command line arguments from $argv
     */
    public function __construct(array $args)
    {
        $this->setArguments($args);
    }

    /**
     * Parse command line arguments into key-value pairs
     *
     * @param array $args Raw command line arguments
     * @return void
     */
    private static function setArguments(array $args): void
    {
        $args = array_slice($args, 1);
        $parsedArgs = [];
        foreach ($args as $arg) {
            if (str_contains($arg, '=')) {
                [$key, $value] = explode('=', $arg, 2);
                $parsedArgs[trim($key, '-')] = trim($value, '"');
            }
        }

        self::$arguments = $parsedArgs;
    }

    /**
     * @return array
     */
    public static function getArguments(): array
    {
        return self::$arguments;
    }
}

if (isset($argv)) {
    Argument::getInstance($argv);
}