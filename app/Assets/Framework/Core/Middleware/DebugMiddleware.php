<?php

declare(strict_types=1);

namespace App\Assets\Framework\Core\Middleware;

use App\Assets\Framework\Core\Response\Response;
use App\Assets\Helpers\Http\Request;
use Closure;

class DebugMiddleware extends CoreMiddleware
{
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
