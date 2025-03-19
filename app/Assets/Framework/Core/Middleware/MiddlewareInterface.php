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

namespace App\Assets\Framework\Core\Middleware;

use App\Assets\Framework\Core\Response\Response;
use App\Assets\Helpers\Http\Request;
use Closure;

/**************************************************************************************
 * Interface for defining middleware components in the framework
 *
 * Middleware provides a mechanism for filtering HTTP requests entering
 * the application or modifying responses before they're returned to the client.
 *
 * @package App\Assets\Framework\Core\Middleware;
 */
interface MiddlewareInterface
{
    /**
     * Process an incoming server request
     *
     * Process an incoming server request and return a response, passing along the
     * request to the next middleware in the stack if needed.
     *
     * @param Request $request The request object
     * @param Closure $next The next middleware handler
     * @return Response The response object
     */
    public function process(Request $request, Closure $next): Response;
}
