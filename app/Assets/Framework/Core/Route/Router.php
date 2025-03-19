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

namespace App\Assets\Framework\Core\Route;

use App\Assets\Framework\Core\Middleware\MiddlewareStack;
use App\Assets\Framework\Core\Response\Response;
use App\Assets\Framework\Core\Response\ViewResponse;
use App\Assets\Framework\Exceptions\MethodNotAllowedException;
use App\Assets\Framework\Exceptions\RouteNotFoundException;
use App\Assets\Framework\Traits\SingletonTrait;
use App\Assets\Helpers\Http\Request;
use App\Assets\Helpers\Log\Logger;
use Exception;

/**************************************************************************************
 * Router class for handling HTTP routing
 *
 * Responsible for registering routes, matching URL patterns, and dispatching
 * to appropriate controllers or handlers.
 *
 * @package App\Assets\Framework\Core;
 */
class Router
{
    use SingletonTrait;

    /**
     * Collection of registered routes
     *
     * @var RouteCollection
     */
    private RouteCollection $routes;

    /**
     * Route dispatcher for matching and executing routes
     *
     * @var RouteDispatcher
     */
    private RouteDispatcher $dispatcher;

    /**
     * Middleware stack for route processing
     *
     * @var MiddlewareStack
     */
    private MiddlewareStack $middleware;

    /**
     * Current route group attributes
     *
     * @var array
     */
    private array $groupAttributes = [];

    /**
     * Flag indicating if routes are cached
     *
     * @var bool
     */
    private bool $routesCached = false;

    /**
     * Path to the route cache file
     *
     * @var string
     */
    private string $cacheFile;

    /**
     * Router constructor
     */
    protected function __construct()
    {
        $this->routes = new RouteCollection();
        $this->dispatcher = new RouteDispatcher();
        $this->middleware = new MiddlewareStack();
        $this->cacheFile = PD . DS . 'cache' . DS . 'routes.cache.php';
    }

    /**
     * Register a GET route
     *
     * @param string $pattern Route URL pattern
     * @param mixed $handler Route handler (controller@method, callable, etc.)
     * @return Route Created route instance
     */
    public function get(string $pattern, mixed $handler): Route
    {
        return $this->addRoute(['GET', 'HEAD'], $pattern, $handler);
    }

    /**
     * Register a POST route
     *
     * @param string $pattern Route URL pattern
     * @param mixed $handler Route handler (controller@method, callable, etc.)
     * @return Route Created route instance
     */
    public function post(string $pattern, mixed $handler): Route
    {
        return $this->addRoute(['POST'], $pattern, $handler);
    }

    /**
     * Register a PUT route
     *
     * @param string $pattern Route URL pattern
     * @param mixed $handler Route handler (controller@method, callable, etc.)
     * @return Route Created route instance
     */
    public function put(string $pattern, mixed $handler): Route
    {
        return $this->addRoute(['PUT'], $pattern, $handler);
    }

    /**
     * Register a DELETE route
     *
     * @param string $pattern Route URL pattern
     * @param mixed $handler Route handler (controller@method, callable, etc.)
     * @return Route Created route instance
     */
    public function delete(string $pattern, mixed $handler): Route
    {
        return $this->addRoute(['DELETE'], $pattern, $handler);
    }

    /**
     * Register a PATCH route
     *
     * @param string $pattern Route URL pattern
     * @param mixed $handler Route handler (controller@method, callable, etc.)
     * @return Route Created route instance
     */
    public function patch(string $pattern, mixed $handler): Route
    {
        return $this->addRoute(['PATCH'], $pattern, $handler);
    }

    /**
     * Register a OPTIONS route
     *
     * @param string $pattern Route URL pattern
     * @param mixed $handler Route handler (controller@method, callable, etc.)
     * @return Route Created route instance
     */
    public function options(string $pattern, mixed $handler): Route
    {
        return $this->addRoute(['OPTIONS'], $pattern, $handler);
    }

    /**
     * Register a route that responds to any HTTP method
     *
     * @param string $pattern Route URL pattern
     * @param mixed $handler Route handler (controller@method, callable, etc.)
     * @return Route Created route instance
     */
    public function any(string $pattern, mixed $handler): Route
    {
        return $this->addRoute(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS', 'HEAD'], $pattern, $handler);
    }

    /**
     * Register a route that responds to multiple HTTP methods
     *
     * @param array $methods Array of HTTP methods
     * @param string $pattern Route URL pattern
     * @param mixed $handler Route handler (controller@method, callable, etc.)
     * @return Route Created route instance
     */
    public function match(array $methods, string $pattern, mixed $handler): Route
    {
        return $this->addRoute($methods, $pattern, $handler);
    }

    /**
     * Register a route that renders a view directly
     *
     * @param string $pattern Route URL pattern
     * @param string $view View name
     * @param array $data View data
     * @return Route Created route instance
     */
    public function view(string $pattern, string $view, array $data = []): Route
    {
        return $this->get($pattern, function () use ($view, $data) {
            // This will be implemented when the view system is created
            return new ViewResponse($view, $data);
        });
    }

    /**
     * Register a resource route group for CRUD operations
     *
     * @param string $name Resource name
     * @param string $controller Controller class
     * @param array $options Resource options
     * @return void
     */
    public function resource(string $name, string $controller, array $options = []): void
    {
        // Default resource routes
        $resourceRoutes = [
            'index' => ['GET', "{$name}", 'index'],
            'create' => ['GET', "{$name}/create", 'create'],
            'store' => ['POST', "{$name}", 'store'],
            'show' => ['GET', "{$name}/{id}", 'show'],
            'edit' => ['GET', "{$name}/{id}/edit", 'edit'],
            'update' => ['PUT', "{$name}/{id}", 'update'],
            'destroy' => ['DELETE', "{$name}/{id}", 'destroy'],
        ];

        // Filter out routes based on options
        if (isset($options['only'])) {
            $resourceRoutes = array_intersect_key($resourceRoutes, array_flip((array)$options['only']));
        }

        if (isset($options['except'])) {
            $resourceRoutes = array_diff_key($resourceRoutes, array_flip((array)$options['except']));
        }

        // Register each resource route
        foreach ($resourceRoutes as $route) {
            [$method, $uri, $action] = $route;
            $this->addRoute([$method], $uri, "{$controller}@{$action}");
        }
    }

    /**
     * Create a route group with shared attributes
     *
     * @param array $attributes Shared group attributes
     * @param callable $callback Callback to define routes within group
     * @return void
     */
    public function group(array $attributes, callable $callback): void
    {
        // Save current group attributes
        $previousGroupAttributes = $this->groupAttributes;

        // Merge with new attributes
        $this->groupAttributes = RouteGroup::mergeAttributes(
            $previousGroupAttributes,
            $attributes
        );

        // Execute callback to register routes within group
        $callback($this);

        // Restore previous attributes
        $this->groupAttributes = $previousGroupAttributes;
    }

    /**
     * Dispatch the request to the appropriate route
     *
     * @param Request $request The HTTP request to dispatch
     * @return Response The response from the route handler
     * @throws RouteNotFoundException If no matching route is found
     * @throws MethodNotAllowedException If method is not allowed for the route
     * @throws Exception For other routing errors
     */
    public function dispatch(Request $request): Response
    {
        // Load routes from cache in production
        if (IS_PRODUCTION && !$this->routesCached) {
            $this->loadCachedRoutes();
        }

        try {
            // Log routing attempt
            Logger::getInstance()->debug('Dispatching route', [
                'uri' => $request->getUri(),
                'method' => $request->getMethod()
            ]);

            // Dispatch the request
            return $this->dispatcher->dispatch($request, $this->routes, $this->middleware);
        } catch (RouteNotFoundException|MethodNotAllowedException $e) {
            // Re-throw routing exceptions for handling at a higher level
            throw $e;
        } catch (Exception $e) {
            Logger::getInstance()->error('Route dispatch error', [
                'exception' => $e->getMessage(),
                'uri' => $request->getUri(),
                'method' => $request->getMethod()
            ]);
            throw $e;
        }
    }

    /**
     * Add global middleware to be applied to all routes
     *
     * @param string|callable $middleware Middleware to add
     * @return self For method chaining
     */
    public function addMiddleware(string|callable $middleware): self
    {
        $this->middleware->add($middleware);
        return $this;
    }

    /**
     * Generate a URL for a named route
     *
     * @param string $name Route name
     * @param array $parameters Route parameters
     * @param bool $absolute Whether to generate absolute URL
     * @return string Generated URL
     * @throws RouteNotFoundException If named route doesn't exist
     */
    public function url(string $name, array $parameters = [], bool $absolute = false): string
    {
        return $this->routes->getUrlGenerator()->generate($name, $parameters, $absolute);
    }

    /**
     * Add a route to the collection
     *
     * @param array $methods Allowed HTTP methods
     * @param string $pattern Route pattern
     * @param mixed $handler Route handler
     * @return Route Created route instance
     */
    protected function addRoute(array $methods, string $pattern, mixed $handler): Route
    {
        // Apply group attributes
        $groupPrefix = $this->groupAttributes['prefix'] ?? '';
        if ($groupPrefix) {
            $pattern = $groupPrefix . ($pattern !== '/' ? $pattern : '');
        }

        // Create the route
        $route = new Route($methods, $pattern, $handler);

        // Apply group middleware
        if (isset($this->groupAttributes['middleware'])) {
            $route->middleware($this->groupAttributes['middleware']);
        }

        // Apply group namespace
        if (isset($this->groupAttributes['namespace'])) {
            $route->namespace($this->groupAttributes['namespace']);
        }

        // Add route to collection
        $this->routes->add($route);

        return $route;
    }

    /**
     * Load routes from cache file
     *
     * @return bool Success status
     */
    public function loadCachedRoutes(): bool
    {
        if (file_exists($this->cacheFile)) {
            $this->routes = require $this->cacheFile;
            $this->routesCached = true;
            return true;
        }
        return false;
    }

    /**
     * Cache all registered routes to a file
     *
     * @return bool Success status
     */
    public function cacheRoutes(): bool
    {
        // Create cache directory if it doesn't exist
        $cacheDir = dirname($this->cacheFile);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $routeCache = '<?php return ' . var_export($this->routes, true) . ';';
        $result = file_put_contents($this->cacheFile, $routeCache);
        return $result !== false;
    }

    /**
     * Clear the route cache
     *
     * @return bool Success status
     */
    public function clearRouteCache(): bool
    {
        if (file_exists($this->cacheFile)) {
            return unlink($this->cacheFile);
        }
        return true;
    }

    /**
     * Get all registered routes
     *
     * @return RouteCollection
     */
    public function getRoutes(): RouteCollection
    {
        return $this->routes;
    }
}
