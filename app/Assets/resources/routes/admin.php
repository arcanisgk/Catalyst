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

use App\Assets\Framework\Core\Route\Router;

// Get router instance
$router = Router::getInstance();

// Admin routes with admin prefix and middleware
$router->group([
    'prefix' => 'admin',
    'middleware' => ['auth', 'admin'],
    'namespace' => 'Admin'
], function ($router) {

    // Dashboard
    $router->get('/', 'DashboardController@index')->name('admin.dashboard');

    // User management
    $router->get('/users', 'UserController@index')->name('admin.users.index');
    $router->get('/users/create', 'UserController@create')->name('admin.users.create');
    $router->post('/users', 'UserController@store')->name('admin.users.store');
    $router->get('/users/{id}', 'UserController@show')->name('admin.users.show');
    $router->get('/users/{id}/edit', 'UserController@edit')->name('admin.users.edit');
    $router->put('/users/{id}', 'UserController@update')->name('admin.users.update');
    $router->delete('/users/{id}', 'UserController@destroy')->name('admin.users.destroy');

    // Content management
    $router->get('/articles', 'ArticleController@index')->name('admin.articles.index');
    $router->get('/articles/create', 'ArticleController@create')->name('admin.articles.create');
    $router->post('/articles', 'ArticleController@store')->name('admin.articles.store');
    $router->get('/articles/{id}/edit', 'ArticleController@edit')->name('admin.articles.edit');
    $router->put('/articles/{id}', 'ArticleController@update')->name('admin.articles.update');
    $router->delete('/articles/{id}', 'ArticleController@destroy')->name('admin.articles.destroy');

    // Settings
    $router->get('/settings', 'SettingController@index')->name('admin.settings');
    $router->post('/settings', 'SettingController@update')->name('admin.settings.update');

    // System information
    $router->get('/system', 'SystemController@index')->name('admin.system');
    $router->post('/system/clear-cache', 'SystemController@clearCache')->name('admin.system.clear-cache');
    $router->post('/system/maintenance/{mode}', 'SystemController@maintenance')
        ->name('admin.system.maintenance')
        ->where('mode', 'on|off');
});
