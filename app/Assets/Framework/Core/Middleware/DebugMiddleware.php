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

namespace Catalyst\Framework\Core\Middleware;

use Catalyst\Framework\Core\Http\Request;
use Catalyst\Framework\Core\Response\Response;
use Closure;
use Exception;

/**************************************************************************************
 * Middleware that provides request and response debugging capabilities.
 *
 * This middleware logs detailed information about incoming requests and outgoing responses
 * when the application is running in development mode. It captures timing information,
 * request parameters, and response status to aid in debugging application flow.
 *
 * @package Catalyst\Framework\Core\Middleware
 * @since 1.0.0
 */
class DebugMiddleware extends CoreMiddleware
{
    /**
     * @throws Exception
     */
    public function process(Request $request, Closure $next): Response
    {
        // Log request information in development
        $this->log('Debug middleware processing request', [
            'uri' => $_SERVER['REQUEST_URI'] ?? '/',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
            'time' => microtime(true)
        ]);

        // Process the request
        $response = $this->passToNext($request, $next);

        // Log response information
        $this->log('Debug middleware received response', [
            'status' => $response->getStatusCode(),
            'time' => microtime(true)
        ]);

        return $response;
    }
}
