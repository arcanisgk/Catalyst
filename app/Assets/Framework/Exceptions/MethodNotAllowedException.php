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

namespace App\Assets\Framework\Exceptions;

use RuntimeException;
use Throwable;

/**************************************************************************************
 * Exception thrown when an HTTP method is not allowed for a route
 *
 * This exception is thrown when a route exists for the requested URI but the
 * HTTP method used is not allowed for that route (405 Method Not Allowed).
 *
 * @package App\Assets\Framework\Exceptions;
 */
class MethodNotAllowedException extends RuntimeException
{
    /**
     * HTTP methods that are allowed for the route
     *
     * @var array
     */
    private array $allowedMethods;

    /**
     * Create a new method not allowed exception
     *
     * @param string $message Exception message
     * @param array $allowedMethods HTTP methods allowed for this route
     * @param int $code Exception code
     * @param Throwable|null $previous Previous exception
     */
    public function __construct(
        string     $message = 'Method not allowed',
        array      $allowedMethods = [],
        int        $code = 405,
        ?Throwable $previous = null
    )
    {
        $this->allowedMethods = $allowedMethods;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the HTTP methods that are allowed for the route
     *
     * @return array Array of allowed HTTP methods
     */
    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }
}