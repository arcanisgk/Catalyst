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

use Catalyst\Framework\Core\Route\Router;

// Get router instance
$router = Router::getInstance();

// API route group with common prefix and middleware
/*
$router->group([
    'prefix' => 'api',
    'middleware' => ['api', 'throttle']
], function ($router) {

    // API version 1
    $router->group(['prefix' => 'v1'], function ($router) {

        // Authentication endpoints
        $router->post('/auth/login', 'Api\AuthController@login');
        $router->post('/auth/register', 'Api\AuthController@register');

        // Public endpoints
        $router->get('/products', 'Api\ProductController@index');
        $router->get('/products/{id}', 'Api\ProductController@show')
            ->where('id', '[0-9]+');

        // Protected endpoints
        $router->group(['middleware' => 'auth:api'], function ($router) {
            // User profile
            $router->get('/user', 'Api\UserController@currentUser');
            $router->put('/user', 'Api\UserController@update');

            // CRUD for user resources
            $router->get('/orders', 'Api\OrderController@index');
            $router->post('/orders', 'Api\OrderController@store');
            $router->get('/orders/{id}', 'Api\OrderController@show');
            $router->put('/orders/{id}', 'Api\OrderController@update');
            $router->delete('/orders/{id}', 'Api\OrderController@destroy');
        });
    });

    // API version 2 (for future use)
    $router->group(['prefix' => 'v2'], function ($router) {
        $router->get('/', function () {
            return json_success(['message' => 'API v2 coming soon']);
        });
    });
});
*/