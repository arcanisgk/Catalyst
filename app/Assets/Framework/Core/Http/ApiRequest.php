<?php

declare(strict_types=1);

namespace Catalyst\Framework\Core\Http;

use Catalyst\Assets\Framework\Core\Http\Request;

class ApiRequest
{
    /**
     * Procesa una petición API, extrayendo datos según el formato
     *
     * @param Request $request La petición original
     * @return array Los datos de la petición
     */
    public static function getData(Request $request): array
    {
        $contentType = $request->getHeaders('Content-Type');

        if ($contentType && str_contains($contentType, 'application/json')) {
            // JSON request body
            return json_decode($request->getContent(), true) ?? [];
        } elseif ($contentType && str_contains($contentType, 'application/x-www-form-urlencoded')) {
            // Form URL-encoded
            return $request->getAllPost();
        } elseif ($contentType && str_contains($contentType, 'multipart/form-data')) {
            // Multipart form data
            return $request->getAllPost();
        }

        // Default fallback
        return $request->getAllPost();
    }

    /**
     * Determina si la petición es una API request
     *
     * @param Request $request La petición
     * @return bool True si es API request
     */
    public static function isApiRequest(Request $request): bool
    {
        // Check X-Requested-With header (standard AJAX indicator)
        $requestedWith = $request->getHeaders('X-Requested-With');
        if ($requestedWith && strtolower($requestedWith) === 'xmlhttprequest') {
            return true;
        }

        // Check Accept header for application/json
        $accept = $request->getHeaders('Accept');
        if ($accept && str_contains($accept, 'application/json')) {
            return true;
        }

        // Check Content-Type for application/json
        $contentType = $request->getHeaders('Content-Type');
        if ($contentType && str_contains($contentType, 'application/json')) {
            return true;
        }

        return false;
    }
}