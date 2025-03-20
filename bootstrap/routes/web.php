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


$router->group(['namespace' => 'Catalyst\Framework\Controllers'], function ($router) {
    // Define basic web routes
    $router->get('/', 'HomeController@index')->name('home');
    $router->get('/about', 'HomeController@about')->name('about');
    $router->get('/contact', 'ContactController@index')->name('contact');
    $router->post('/contact', 'ContactController@submit')->name('contact.submit');
});

// Routes with parameters
$router->get('/articles/{id}', 'ArticleController@show')->name('articles.show')
    ->where('id', '[0-9]+'); // Add constraint: id must be numeric

// Optional parameters example
$router->get('/products/{category?}', 'ProductController@index')->name('products.index');

// Multiple parameters
$router->get('/categories/{category}/products/{id}', 'ProductController@show')
    ->name('products.show');

// Auth routes
$router->group(['prefix' => 'auth'], function ($router) {
    $router->get('/login', 'AuthController@loginForm')->name('auth.login');
    $router->post('/login', 'AuthController@login')->name('auth.login.post');
    $router->get('/register', 'AuthController@registerForm')->name('auth.register');
    $router->post('/register', 'AuthController@register')->name('auth.register.post');
    $router->post('/logout', 'AuthController@logout')->name('auth.logout');
});

// Example of routes with middleware
$router->group(['middleware' => 'auth'], function ($router) {
    $router->get('/dashboard', 'DashboardController@index')->name('dashboard');
    $router->get('/profile', 'ProfileController@show')->name('profile');
    $router->put('/profile', 'ProfileController@update')->name('profile.update');
});
