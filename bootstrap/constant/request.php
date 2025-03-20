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


if (!defined('RQ') && isset($_SERVER['REQUEST_METHOD'])) {
    /**
     * Checks if the request method constant is not already defined and the request method is set.
     * Prepares to define a constant for the current HTTP request method.
     */
    define('RQ', $_SERVER['REQUEST_METHOD']);
}


if (!defined('UR') && isset($_SERVER['REQUEST_URI'])) {
    /**
     * Checks if the request URI constant is not already defined and the request URI is set.
     * Prepares to define a constant for the current HTTP request URI.
     */
    define('UR', $_SERVER['REQUEST_URI']);
}