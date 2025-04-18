<?php

declare(strict_types=1);

/**************************************************************************************
 *
 * Catalyst PHP Framework
 * PHP Version 8.3 (Required).
 *
 * @package   Catalyst
 * @subpackage Public
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
 */


use Catalyst\Framework\Core\Route\Router;

$router = Router::getInstance();

$router->group(['namespace' => 'Catalyst\Solution\Controllers'], function ($router) {
    $router->get('/', 'HomeController@landing')->name('landing');
    $router->group(['prefix' => 'configure', 'middleware' => 'Catalyst\Framework\Core\Middleware\BasicAuthMiddleware'], function ($router) {
        $router->get('/', 'ConfigController@index')->name('config.index');
        $router->get('/{section}', 'ConfigController@showSection')->name('config.section');
        $router->post('/{section}/save', 'ConfigController@saveConfig')->name('config.save');
        $router->get('/check-dkim-keys', 'ConfigController@checkDkimKeys');
        $router->post('/generate-dkim-keys', 'ConfigController@generateDkimKeys');
        $router->post('/test-connection', 'ConfigController@testConnection')->name('config.test');
        $router->post('/change-environment', 'ConfigController@changeEnvironment')->name('config.environment');
    });
});


/*
$router->group(['namespace' => 'Catalyst\Solution\Controllers'], function ($router) {

    $router->get('/configure/oauth/credentials/{service}', 'ConfigController@getOAuthCredentials')->name('oauth.credentials');
    $router->post('/configure/oauth/save', 'ConfigController@saveOAuthCredentials')->name('oauth.save');
    $router->post('/configure/oauth/clear', 'ConfigController@clearOAuthCredentials')->name('oauth.clear');

    $router->get('/configure', 'ConfigController@index')->name('config.index');
    $router->get('/configure/{section}', 'ConfigController@showSection')->name('config.section');
    $router->post('/configure/{section}/save', 'ConfigController@saveConfig')->name('config.save');

    // Add this route with your other configuration routes
    $router->get('/configure/check-dkim-keys', 'ConfigController@checkDkimKeys');

    $router->post('/configure/generate-dkim-keys', 'ConfigController@generateDkimKeys');
    $router->post('/configure/test-connection', 'ConfigController@testConnection')->name('config.test');
    $router->post('/configure/change-environment', 'ConfigController@changeEnvironment')->name('config.environment');

    $router->get('/', 'HomeController@landing')->name('landing');
    $router->get('/home', 'HomeController@index')->name('home');
});
/*
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
*/