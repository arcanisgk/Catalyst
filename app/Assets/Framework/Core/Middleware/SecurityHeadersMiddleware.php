<?php

declare(strict_types=1);

namespace Catalyst\Framework\Core\Middleware;

use Catalyst\Assets\Framework\Core\Http\Request;
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

        if (IS_PRODUCTION) {
            $response->setHeader('Content-Security-Policy', "default-src 'self'");
        }

        return $response;
    }
}
