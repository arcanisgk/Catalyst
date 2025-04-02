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
 * RouteNotFoundException component for the Catalyst Framework
 *
 */

namespace Catalyst\Framework\Core\Exceptions;

use RuntimeException;
use Throwable;

/**************************************************************************************
 * Exception thrown when a route cannot be found
 *
 * This exception is thrown when a route matching the requested URI is not found
 * or when a named route doesn't exist.
 *
 * @package Catalyst\Framework\Core\Exceptions;
 */
class RouteNotFoundException extends RuntimeException
{
    /**
     * Create a new route not found exception
     *
     * @param string $message Exception message
     * @param int $code Exception code
     * @param Throwable|null $previous Previous exception
     */
    public function __construct(
        string     $message = 'Route not found',
        int        $code = 404,
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}
