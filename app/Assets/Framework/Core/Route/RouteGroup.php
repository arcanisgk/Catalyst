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

/**************************************************************************************
 * RouteGroup class for handling route grouping functionality
 *
 * Manages grouping of routes with shared attributes like prefixes, middleware,
 * and namespaces.
 *
 * @package Catalyst\Framework\Core\Route;
 */
class RouteGroup
{
    /**
     * Merge parent and child group attributes
     *
     * @param array $parentAttributes Parent group attributes
     * @param array $childAttributes Child group attributes
     * @return array Merged attributes
     */
    public static function mergeAttributes(array $parentAttributes, array $childAttributes): array
    {
        $mergedAttributes = $parentAttributes;

        // Merge prefixes by concatenating them
        if (isset($childAttributes['prefix'])) {
            $prefix = $childAttributes['prefix'];

            // Ensure prefix starts with a slash if non-empty
            if (!empty($prefix) && $prefix[0] !== '/') {
                $prefix = '/' . $prefix;
            }

            // Concatenate with parent prefix if it exists
            if (isset($parentAttributes['prefix'])) {
                $mergedAttributes['prefix'] = rtrim($parentAttributes['prefix'], '/') . $prefix;
            } else {
                $mergedAttributes['prefix'] = $prefix;
            }
        }

        // Merge namespaces by concatenating with backslash separator
        if (isset($childAttributes['namespace'])) {
            if (isset($parentAttributes['namespace'])) {
                $mergedAttributes['namespace'] = rtrim($parentAttributes['namespace'], '\\') . '\\' .
                    ltrim($childAttributes['namespace'], '\\');
            } else {
                $mergedAttributes['namespace'] = $childAttributes['namespace'];
            }
        }

        // Merge middleware by combining arrays
        if (isset($childAttributes['middleware'])) {
            $childMiddleware = is_array($childAttributes['middleware'])
                ? $childAttributes['middleware']
                : [$childAttributes['middleware']];

            if (isset($parentAttributes['middleware'])) {
                $parentMiddleware = is_array($parentAttributes['middleware'])
                    ? $parentAttributes['middleware']
                    : [$parentAttributes['middleware']];

                $mergedAttributes['middleware'] = array_merge($parentMiddleware, $childMiddleware);
            } else {
                $mergedAttributes['middleware'] = $childMiddleware;
            }
        }

        // Merge domain if specified
        if (isset($childAttributes['domain'])) {
            $mergedAttributes['domain'] = $childAttributes['domain'];
        }

        // Merge where constraints
        if (isset($childAttributes['where'])) {
            if (isset($parentAttributes['where'])) {
                $mergedAttributes['where'] = array_merge($parentAttributes['where'], $childAttributes['where']);
            } else {
                $mergedAttributes['where'] = $childAttributes['where'];
            }
        }

        // Merge any other attributes by overwriting parent with child values
        foreach ($childAttributes as $key => $value) {
            if (!in_array($key, ['prefix', 'namespace', 'middleware', 'domain', 'where'])) {
                $mergedAttributes[$key] = $value;
            }
        }

        return $mergedAttributes;
    }

    /**
     * Apply group attributes to a route pattern
     *
     * @param string $pattern Route pattern
     * @param array $attributes Group attributes
     * @return string Modified route pattern
     */
    public static function applyAttributesToPattern(string $pattern, array $attributes): string
    {
        // Apply prefix if exists
        if (isset($attributes['prefix'])) {
            $prefix = $attributes['prefix'];

            // Ensure prefix starts with a slash
            if (!empty($prefix) && $prefix[0] !== '/') {
                $prefix = '/' . $prefix;
            }

            // Concatenate prefix with pattern
            $pattern = rtrim($prefix, '/') . ($pattern === '/' ? '' : $pattern);
        }

        // Apply domain if exists
        if (isset($attributes['domain'])) {
            // Domain handling would be implemented here if needed
            // This would typically modify how the route is matched
        }

        return $pattern;
    }

    /**
     * Get middleware from group attributes
     *
     * @param array $attributes Group attributes
     * @return array Middleware array
     */
    public static function getMiddleware(array $attributes): array
    {
        if (!isset($attributes['middleware'])) {
            return [];
        }

        return is_array($attributes['middleware'])
            ? $attributes['middleware']
            : [$attributes['middleware']];
    }

    /**
     * Get namespace from group attributes
     *
     * @param array $attributes Group attributes
     * @return string|null Namespace string or null
     */
    public static function getNamespace(array $attributes): ?string
    {
        return $attributes['namespace'] ?? null;
    }

    /**
     * Get constraints from group attributes
     *
     * @param array $attributes Group attributes
     * @return array Constraints array
     */
    public static function getConstraints(array $attributes): array
    {
        return $attributes['where'] ?? [];
    }
}
