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
 * SecurityHeadersMiddleware component for the Catalyst Framework
 *
 */

namespace Catalyst\Framework\Core\Middleware;


use Catalyst\Framework\Core\Http\Request;
use Catalyst\Framework\Core\Response\Response;
use Closure;
use Exception;

class SecurityHeadersMiddleware extends CoreMiddleware
{
    /**
     * @throws Exception
     */
    public function process(Request $request, Closure $next): Response
    {
        // Get the response from the next middleware or handler
        $response = $this->passToNext($request, $next);

        // Add security headers
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        $response->setHeader('X-XSS-Protection', '1; mode=block');
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        $response->setHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Add Cross-Origin headers
        $response->setHeader('Cross-Origin-Opener-Policy', 'same-origin');
        $response->setHeader('Cross-Origin-Embedder-Policy', 'require-corp');
        $response->setHeader('Cross-Origin-Resource-Policy', 'same-origin');

        if (defined('IS_PRODUCTION') && IS_PRODUCTION) {
            $response->setHeader('Content-Security-Policy',
                "default-src 'self'; " .
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; " .
                "script-src 'self' 'unsafe-inline'; " .
                "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; " .
                "img-src 'self' data:;"
            );

            $response->setHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
