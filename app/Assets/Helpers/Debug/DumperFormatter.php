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
 * DumperFormatter component for the Catalyst Framework
 *
 */

namespace Catalyst\Helpers\Debug;

/**************************************************************************************
 * DumperFormatter class for formatting different variable types
 *
 * This class is responsible for formatting different types of variables
 * for display in the debug output. It handles strings, numbers, booleans,
 * arrays, objects, resources, and null values.
 *
 * @package Catalyst\Helpers\Debug;
 */
class DumperFormatter
{
    /**
     * DumperConfig instance
     */
    private DumperConfig $config;

    /**
     * DumperColorizer instance
     */
    private DumperColorizer $colorizer;

    /**
     * DumperCollapsible instance
     */
    private DumperCollapsible $collapsible;

    /**
     * Constructor
     *
     * @param DumperConfig $config Configuration instance
     * @param DumperColorizer $colorizer Colorizer instance
     * @param DumperCollapsible $collapsible Collapsible instance
     */
    public function __construct(
        DumperConfig      $config,
        DumperColorizer   $colorizer,
        DumperCollapsible $collapsible
    )
    {
        $this->config = $config;
        $this->colorizer = $colorizer;
        $this->collapsible = $collapsible;
    }

    /**
     * Format and output the variable
     *
     * @param mixed $var Variable to dump
     * @param string $label Variable label
     * @param bool $isHtml Whether to format for HTML output
     * @param int $depth Current depth level
     * @return string Formatted output
     */
    public function formatVar(mixed $var, string $label = '', bool $isHtml = true, int $depth = 0): string
    {
        $indent = str_repeat('    ', $depth);
        $type = gettype($var);

        $typeColor = $this->colorizer->getTypeColor($type, $isHtml);
        $labelOutput = $label ? $this->colorizer->colorize($label . ' ', 'label', $isHtml) : '';

        // For arrays and objects, we'll handle the type display differently
        if ($type === 'array' || $type === 'object') {
            // For arrays and objects, we'll include the type in the header of the collapsible section
            $valueDisplay = match ($type) {
                'array' => $this->formatArray($var, $isHtml, $depth),
                'object' => $this->formatObject($var, $isHtml, $depth),
                default => '' // This won't happen but keeps the match expression valid
            };

            // Return just the label and the formatted array/object without the type indicator
            // The type is already included in the collapsible header
            return $labelOutput . $valueDisplay;
        } else {
            // For other types, proceed as before
            $valueDisplay = match ($type) {
                'string' => $this->formatString($var, $isHtml),
                'integer', 'double' => $this->formatNumber($var, $isHtml),
                'boolean' => $this->formatBoolean($var, $isHtml),
                'NULL' => $this->formatNull($isHtml),
                'resource' => $this->formatResource($var, $isHtml),
                default => "($type)"
            };

            // Construct the output without any extra spaces
            $output = $labelOutput . $this->colorizer->colorize("($type)", $typeColor, $isHtml);

            // Only add a space if we have a value to display
            if ($valueDisplay) {
                $output .= ' ' . $valueDisplay;
            }

            // Add indentation only if explicitly requested
            if ($depth > 0) {
                return $indent . $output;
            }

            return $output;
        }
    }

    /**
     * Format string for output
     *
     * @param string $var
     * @param bool $isHtml
     * @return string
     */
    public function formatString(string $var, bool $isHtml): string
    {
        $length = strlen($var);

        // Handle multiline strings
        if (str_contains($var, "\n")) {
            $lines = explode("\n", $var);
            $firstLine = htmlspecialchars($lines[0], ENT_QUOTES | ENT_HTML5);
            $result = $this->colorizer->colorize('"' . $firstLine, 'string', $isHtml);

            // Indent and append remaining lines
            for ($i = 1; $i < count($lines); $i++) {
                $line = htmlspecialchars($lines[$i], ENT_QUOTES | ENT_HTML5);
                $result .= "\n" . str_repeat(' ', 8) . $this->colorizer->colorize($line, 'string', $isHtml);
            }

            $result .= $this->colorizer->colorize('"', 'string', $isHtml) .
                $this->colorizer->colorize(" (length=" . $length . ", multiline)", 'meta', $isHtml);

            return $result;
        }

        // Handle regular strings
        $var = htmlspecialchars($var, ENT_QUOTES | ENT_HTML5);

        if ($length > $this->config->getMaxStrLength()) {
            $var = substr($var, 0, $this->config->getMaxStrLength()) . '...';
        }

        return $this->colorizer->colorize('"' . $var . '"', 'string', $isHtml) .
            $this->colorizer->colorize(" (length=" . $length . ")", 'meta', $isHtml);
    }

    /**
     * Format numeric value for output
     *
     * @param int|float $var
     * @param bool $isHtml
     * @return string
     */
    public function formatNumber(int|float $var, bool $isHtml): string
    {
        return $this->colorizer->colorize((string)$var, 'number', $isHtml);
    }

    /**
     * Format boolean for output
     *
     * @param bool $var
     * @param bool $isHtml
     * @return string
     */
    public function formatBoolean(bool $var, bool $isHtml): string
    {
        return $this->colorizer->colorize($var ? 'true' : 'false', 'boolean', $isHtml);
    }

    /**
     * Format null for output
     *
     * @param bool $isHtml
     * @return string
     */
    public function formatNull(bool $isHtml): string
    {
        return $this->colorizer->colorize('null', 'null', $isHtml);
    }

    /**
     * Format resource for output
     *
     * @param resource $var
     * @param bool $isHtml
     * @return string
     */
    public function formatResource($var, bool $isHtml): string
    {
        $resourceType = get_resource_type($var);
        $resourceId = (int)$var;

        return $this->colorizer->colorize(
            "resource($resourceId) of type $resourceType",
            'resource',
            $isHtml
        );
    }

    /**
     * Format array for output
     *
     * @param array $var
     * @param bool $isHtml
     * @param int $depth
     * @return string
     */
    public function formatArray(array $var, bool $isHtml, int $depth): string
    {
        $count = count($var);
        if ($depth >= $this->config->getMaxDepth()) {
            return $this->colorizer->colorize("(array)", 'array', $isHtml) .
                $this->colorizer->colorize(" Array", 'array', $isHtml) .
                $this->colorizer->colorize(" (items=" . $count . ")", 'meta', $isHtml) .
                $this->colorizer->colorize(" [MAX DEPTH REACHED]", 'error', $isHtml);
        }

        $header = $this->colorizer->colorize("(array)", 'array', $isHtml) . ' ' .
            $this->colorizer->colorize("Array", 'array', $isHtml) .
            $this->colorizer->colorize(" (items=" . $count . ")", 'meta', $isHtml);

        // If array is empty, don't make it collapsible
        if ($count === 0) {
            return $header . " {}";
        }

        $contentBuffer = '';
        $i = 0;
        foreach ($var as $key => $value) {
            if ($i >= $this->config->getMaxChildren()) {
                $indent = str_repeat('    ', $depth + 1);
                $contentBuffer .= $indent . $this->colorizer->colorize(
                        "... +" . ($count - $this->config->getMaxChildren()) . " more items",
                        'meta',
                        $isHtml
                    );
                break;
            }

            // Format the key
            $keyDisplay = is_string($key) ?
                $this->colorizer->colorize("\"$key\"", 'key', $isHtml) :
                $this->colorizer->colorize((string)$key, 'key', $isHtml);

            // Get the line indentation
            $lineIndent = str_repeat('    ', $depth + 1);

            // For arrays and objects, we need special handling to maintain proper indentation
            if (is_array($value) || is_object($value)) {
                // Format complex values with proper indentation
                $formattedValue = $this->formatVar($value, '', $isHtml, $depth + 1);
                // Remove any leading spaces that might be added by formatVar
                $formattedValue = ltrim($formattedValue);
                $contentBuffer .= $lineIndent . "[" . $keyDisplay . "] => " . $formattedValue;
            } else {
                // For simple values, format with proper indentation
                $valueFormatted = $this->formatVar($value, '', $isHtml, 0);
                $contentBuffer .= $lineIndent . "[" . $keyDisplay . "] => " . trim($valueFormatted);
            }

            // Add a newline if this is not the last item
            if ($i < min($count - 1, $this->config->getMaxChildren() - 1)) {
                $contentBuffer .= PHP_EOL;
            }

            $i++;
        }

        // Make the array collapsible
        return $this->collapsible->create(
            $header,
            $contentBuffer,
            $isHtml,
            $this->config->getInitiallyExpanded(),
            $depth
        );
    }

    /**
     * Format object for output
     *
     * @param object $var
     * @param bool $isHtml
     * @param int $depth
     * @return string
     */
    public function formatObject(object $var, bool $isHtml, int $depth): string
    {
        $class = get_class($var);
        $props = (array)$var;
        $count = count($props);

        if ($depth >= $this->config->getMaxDepth()) {
            return $this->colorizer->colorize("(object)", 'object', $isHtml) . ' ' .
                $this->colorizer->colorize($class, 'object', $isHtml) .
                $this->colorizer->colorize(" (properties=" . $count . ")", 'meta', $isHtml) .
                $this->colorizer->colorize(" [MAX DEPTH REACHED]", 'error', $isHtml);
        }

        $header = $this->colorizer->colorize("(object)", 'object', $isHtml) . ' ' .
            $this->colorizer->colorize($class, 'object', $isHtml) .
            $this->colorizer->colorize(" (properties=" . $count . ")", 'meta', $isHtml);

        // If object has no properties, don't make it collapsible
        if ($count === 0) {
            return $header . " {}";
        }

        $contentBuffer = '';
        $i = 0;
        foreach ($props as $key => $value) {
            if ($i >= $this->config->getMaxChildren()) {
                $indent = str_repeat('    ', $depth + 1);
                $contentBuffer .= $indent . $this->colorizer->colorize(
                        "... +" . ($count - $this->config->getMaxChildren()) . " more properties",
                        'meta',
                        $isHtml
                    );
                break;
            }

            // Handle property name with potential visibility indicator
            $keyString = (string)$key;
            $keyParts = explode("\0", $keyString);
            $propName = end($keyParts);
            $visibility = 'public';

            if (count($keyParts) > 1) {
                $visibility = $keyParts[1] === '*' ? 'protected' : 'private';
            }

            // Get the line indentation
            $lineIndent = str_repeat('    ', $depth + 1);

            // For arrays and objects, we need special handling to maintain proper indentation
            if (is_array($value) || is_object($value)) {
                // Format complex values with proper indentation
                $formattedValue = $this->formatVar($value, '', $isHtml, $depth + 1);
                // Remove any leading spaces that might be added by formatVar
                $formattedValue = ltrim($formattedValue);

                $contentBuffer .= $lineIndent .
                    $this->colorizer->colorize("[$visibility]", $visibility, $isHtml) . " " .
                    $this->colorizer->colorize("$propName", 'key', $isHtml) . " => " .
                    $formattedValue;
            } else {
                // For simple values, format with proper indentation
                $valueFormatted = $this->formatVar($value, '', $isHtml, 0);
                $contentBuffer .= $lineIndent .
                    $this->colorizer->colorize("[$visibility]", $visibility, $isHtml) . " " .
                    $this->colorizer->colorize("$propName", 'key', $isHtml) . " => " .
                    trim($valueFormatted);
            }

            // Add a newline if this is not the last item
            if ($i < min($count - 1, $this->config->getMaxChildren() - 1)) {
                $contentBuffer .= PHP_EOL;
            }

            $i++;
        }

        // Make the object collapsible
        return $this->collapsible->create(
            $header,
            $contentBuffer,
            $isHtml,
            $this->config->getInitiallyExpanded(),
            $depth
        );
    }
}
