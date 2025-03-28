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

namespace Catalyst\Framework\Core\Route;

use Catalyst\Framework\Core\Exceptions\MethodNotAllowedException;
use Catalyst\Framework\Core\Exceptions\RouteNotFoundException;
use Catalyst\Framework\Core\Http\Request;
use Catalyst\Framework\Core\Middleware\MiddlewareStack;
use Catalyst\Framework\Core\Response\HtmlResponse;
use Catalyst\Framework\Core\Response\JsonResponse;
use Catalyst\Framework\Core\Response\Response;
use Catalyst\Helpers\Log\Logger;
use Closure;
use Exception;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;

/**************************************************************************************
 * RouteDispatcher class for matching and executing routes
 *
 * Handles route matching, controller resolution, parameter binding, and
 * middleware execution.
 *
 * @package Catalyst\Framework\Core\Route;
 */
class RouteDispatcher
{
    /**
     * Dispatch the request to the appropriate route handler
     *
     * @param Request $request HTTP request to dispatch
     * @param RouteCollection $routes Collection of routes to match against
     * @param MiddlewareStack $middleware Global middleware stack
     * @return Response The response from route handler
     * @throws RouteNotFoundException If no matching route is found
     * @throws MethodNotAllowedException If method is not allowed for the route
     * @throws Exception For other errors during dispatching
     */
    public function dispatch(
        Request         $request,
        RouteCollection $routes,
        MiddlewareStack $middleware
    ): Response
    {
        // Extract URI and method from request
        $uri = $request->getUri() ?? '/';
        $method = $request->getMethod() ?? 'GET';

        // Normalize URI by removing query string and trailing slash
        $uri = $this->normalizeUri($uri);

        // Try to match the route
        $routeParams = [];
        $matchedRoute = $routes->match($uri, $method, $routeParams);

        // If no route matched, but we have allowed methods, it's a 405 Method Not Allowed
        if ($matchedRoute === null && isset($routeParams['_allowed_methods'])) {
            throw new MethodNotAllowedException(
                "Method '$method' not allowed for route '$uri'",
                $routeParams['_allowed_methods']
            );
        }

        // If no route matched at all, it's a 404 Not Found
        if ($matchedRoute === null) {
            throw new RouteNotFoundException("No route found for '$uri' with method '$method'");
        }

        // Create middleware stack with route-specific middleware
        $routeMiddleware = $matchedRoute->getMiddleware();
        if (!empty($routeMiddleware)) {
            foreach ($routeMiddleware as $middlewareItem) {
                $middleware->add($middlewareItem);
            }
        }

        // Execute middleware stack with route handler as the core
        return $middleware->process($request, function ($request) use ($matchedRoute, $routeParams) {
            return $this->executeRoute($matchedRoute, $request, $routeParams);
        });
    }

    /**
     * Execute a route handler with the given parameters
     *
     * @param Route $route The matched route
     * @param Request $request The current request
     * @param array $parameters Route parameters
     * @return Response The response from the route handler
     * @throws Exception If handler cannot be resolved or executed
     */
    protected function executeRoute(Route $route, Request $request, array $parameters): Response
    {

        $handler = $route->getHandler();

        // Log the execution
        Logger::getInstance()->debug('Executing route', [
            'pattern' => $route->getPattern(),
            'method' => implode('|', $route->getMethods()),
            'handler' => is_string($handler) ? $handler : 'Closure'
        ]);

        // Resolve the handler to a callable
        if ($handler instanceof Closure) {
            $response = $this->executeClosure($handler, $request, $parameters);
        } elseif (is_string($handler) && str_contains($handler, '@')) {
            $response = $this->executeController($handler, $route->getNamespace(), $request, $parameters);
        } elseif (is_callable($handler)) {
            $response = $handler($request, $parameters);
        } else {
            throw new Exception("Invalid route handler");
        }

        // Convert the response to a Response object if it isn't already
        if (!$response instanceof Response) {
            $response = $this->convertToResponse($response);
        }

        return $response;
    }

    /**
     * Execute a controller method
     *
     * @param string $handler Controller@method string
     * @param string|null $namespace Controller namespace
     * @param Request $request The current request
     * @param array $parameters Route parameters
     * @return mixed The return value from the controller method
     * @throws Exception If controller or method cannot be resolved
     */
    protected function executeController(
        string  $handler,
        ?string $namespace,
        Request $request,
        array   $parameters
    ): mixed
    {
        [$controller, $method] = explode('@', $handler);

        // Apply namespace if provided
        if ($namespace) {
            $controller = rtrim($namespace, '\\') . '\\' . $controller;
        }

        // Check if controller class exists
        if (!class_exists($controller)) {
            throw new Exception("Controller '$controller' not found");
        }

        // Create controller instance
        $controllerInstance = new $controller();

        // Check if method exists
        if (!method_exists($controllerInstance, $method)) {
            throw new Exception("Method '$method' not found in controller '$controller'");
        }

        // Prepare parameters for method
        $methodParams = $this->resolveMethodDependencies(
            new ReflectionMethod($controller, $method),
            $request,
            $parameters
        );

        // Invoke the method with resolved parameters
        return $controllerInstance->$method(...$methodParams);
    }

    /**
     * Execute a closure handler
     *
     * @param Closure $closure The route handler
     * @param Request $request The current request
     * @param array $parameters Route parameters
     * @return mixed The return value from the closure
     * @throws ReflectionException
     * @throws Exception
     */
    protected function executeClosure(Closure $closure, Request $request, array $parameters): mixed
    {
        // Resolve dependencies for the closure
        $reflector = new ReflectionFunction($closure);
        $dependencies = $this->resolveMethodDependencies($reflector, $request, $parameters);

        // Execute the closure with resolved dependencies
        return $closure(...$dependencies);
    }

    /**
     * Resolve method dependencies using reflection
     *
     * @param ReflectionMethod|ReflectionFunction $reflector Method or function reflector
     * @param Request $request The current request
     * @param array $routeParameters Route parameters
     * @return array Resolved parameters for the method
     * @throws ReflectionException
     * @throws Exception
     */
    protected function resolveMethodDependencies(ReflectionMethod|ReflectionFunction $reflector, Request $request, array $routeParameters): array
    {
        $parameters = [];

        foreach ($reflector->getParameters() as $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();

            // If parameter is type-hinted as Request, inject request object
            if ($type && !$type->isBuiltin()) {
                $className = $type->getName();
                if ($request instanceof $className) {
                    $parameters[] = $request;
                    continue;
                }
            }

            // If parameter name matches a route parameter, use that value
            if (isset($routeParameters[$name])) {
                $parameters[] = $routeParameters[$name];
                continue;
            }

            // If parameter is optional and not provided, use default value
            if ($parameter->isOptional()) {
                $parameters[] = $parameter->getDefaultValue();
                continue;
            }

            // If we get here, we couldn't resolve the parameter
            throw new Exception("Could not resolve parameter '$name' for route handler");
        }

        return $parameters;
    }

    /**
     * Convert a raw response to a Response object
     *
     * @param mixed $response Raw response from handler
     * @return HtmlResponse|JsonResponse|Response Proper Response object
     */
    protected function convertToResponse(mixed $response): HtmlResponse|JsonResponse|Response
    {
        if (is_string($response)) {
            return new HtmlResponse($response);
        }

        if (is_array($response) || is_object($response)) {
            return new JsonResponse($response);
        }

        // Create a basic response for other types
        return new Response(
            (string)$response,
            200,
            ['Content-Type' => 'text/plain']
        );
    }

    /**
     * Normalize URI by removing query string and ensuring correct format
     *
     * @param string $uri URI to normalize
     * @return string Normalized URI
     */
    protected function normalizeUri(string $uri): string
    {
        // Remove query string
        if (($queryPos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $queryPos);
        }

        // Ensure URI starts with a slash
        if (empty($uri) || $uri[0] !== '/') {
            $uri = '/' . $uri;
        }

        // Trim trailing slash, but keep it for root URI
        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        return $uri;
    }
}
