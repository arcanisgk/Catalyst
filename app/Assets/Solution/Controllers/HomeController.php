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
 * HomeController component for the Catalyst Framework
 *
 */

namespace Catalyst\Solution\Controllers;

use Catalyst\Framework\Core\Http\Request;
use Catalyst\Framework\Core\Response\ViewResponse;
use Exception;

/**
 * Home Controller
 *
 * Handles main website pages like homepage, about, and contact
 *
 * @package Catalyst\Solution\Controllers
 */
class HomeController extends Controller
{
    /**
     * Display the homepage
     *
     * @param Request $request The current request
     * @return ViewResponse
     * @throws Exception
     */
    public function index(Request $request): ViewResponse
    {
        // Log page access
        $this->logInfo('Home page accessed', [
            'ip' => $request->getClientIp() ?? 'unknown'
        ]);

        // Return view with data and explicitly set layout
        return $this->viewWithLayout('Home.index', [
            'title' => 'Welcome to Catalyst',
            'description' => 'A modern PHP framework for rapid application development',
            'activeMenu' => 'home',
            'pageTitle' => 'Home',
            'pageSubtitle' => 'Dashboard',
            'home_intro' => 'Experience the power of Catalyst Framework version 1.0',
            'home_description' => 'Built with modern PHP practices, Catalyst provides a solid foundation for your web applications with a focus on simplicity and performance.',
            'features' => [
                [
                    'title' => 'Elegant routing system',
                    'description' => 'Define routes with a clean, expressive syntax'
                ],
                [
                    'title' => 'Powerful middleware',
                    'description' => 'Filter HTTP requests with customizable middleware'
                ],
                [
                    'title' => 'Flexible response handling',
                    'description' => 'Create and send various response types with ease'
                ],
                [
                    'title' => 'Fast and lightweight',
                    'description' => 'Optimized for performance without unnecessary bloat'
                ]
            ]
        ], 'default');
    }

    /**
     * Display the about page
     *
     * @return ViewResponse
     * @throws Exception
     */
    public function about(): ViewResponse
    {
        // Log page access
        $this->logInfo('About page accessed', [
            'ip' => $request->getClientIp() ?? 'unknown'
        ]);

        return $this->viewWithLayout('Home.about', [
            'title' => 'About Us',
            'activeMenu' => 'about',
            'pageTitle' => 'About',
            'content' => 'Catalyst is a modern PHP framework designed with simplicity and performance in mind.',
            'team' => [
                ['name' => 'Walter Nuñez', 'role' => 'Founder & Lead Developer'],
                ['name' => 'Catalyst Community', 'role' => 'Contributors']
            ]
        ], 'default');
    }

    /**
     * Display the landing page
     *
     * @return ViewResponse
     * @throws Exception
     */
    public function landing(): ViewResponse
    {
        // Log page access
        $this->logInfo('Landing page accessed', [
            'ip' => $this->request ? ($this->request->getClientIp() ?? 'unknown') : 'unknown'
        ]);

        // Return view with data but using the landing layout
        return $this->viewWithLayout('Home.landing', [
            'title' => 'Catalyst Framework - Modern PHP Development',
            'features' => [
                [
                    'title' => 'Modern MVC Architecture',
                    'description' => 'Clean, modular design with separation of concerns',
                    'icon' => 'layer-group'
                ],
                [
                    'title' => 'Powerful middleware',
                    'description' => 'Filter HTTP requests with customizable middleware',
                    'icon' => 'filter'
                ],
                [
                    'title' => 'Flexible Routing',
                    'description' => 'Intuitive and expressive route definitions',
                    'icon' => 'route'
                ],
                [
                    'title' => 'Fast and Lightweight',
                    'description' => 'Optimized for performance without unnecessary bloat',
                    'icon' => 'bolt'
                ],
                [
                    'title' => 'Multilingual Support',
                    'description' => 'Built-in internationalization for global applications',
                    'icon' => 'language'
                ],
                [
                    'title' => 'Modern PHP Development',
                    'description' => 'Leverages PHP 8.3 features for efficient development',
                    'icon' => 'code'
                ]
            ]
        ], 'landing');
    }
}