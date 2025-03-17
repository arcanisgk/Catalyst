<?php

declare(strict_types=1);

namespace App\Assets\Helpers\Debug;

use App\Assets\Framework\Traits\SingletonTrait;

/**
 * Dumper class for debugging variables
 */
class Dumper
{
    use SingletonTrait;

    /**
     * Maximum string length in output
     */
    private int $maxStrLength = 150;

    /**
     * Maximum array/object children to show
     */
    private int $maxChildren = 25;

    /**
     * Maximum nesting level
     */
    private int $maxDepth = 5;

    /**
     * Dump variables with formatting
     *
     * @param array $options Options array with 'data' containing variables to dump
     * @return void
     */
    public static function dump(array $options): void
    {
        $instance = self::getInstance();
        $data = $options['data'] ?? [];

        if (empty($data)) {
            return;
        }

        $isHtml = !IS_CLI;

        if ($isHtml) {
            echo '<pre style="background-color:#1d1e22; color:#e6e6e6; padding:15px; border-radius:5px; font-family:monospace;">';
        }

        foreach ($data as $var) {
            $instance->dumpVar($var, 'Output', $isHtml);

            if ($isHtml) {
                echo '<hr style="border:1px dashed #505050; margin:10px 0;">';
            } else {
                echo str_repeat('-', min(80, TW)) . PHP_EOL;
            }
        }

        if ($isHtml) {
            echo '</pre>';
        }
    }

    /**
     * Format and output the variable
     *
     * @param mixed $var Variable to dump
     * @param string $label Variable label
     * @param bool $isHtml Whether to format for HTML output
     * @param int $depth Current depth level
     * @return void
     */
    private function dumpVar(mixed $var, string $label = '', bool $isHtml = true, int $depth = 0): void
    {
        $indent = str_repeat('    ', $depth);
        $type = gettype($var);

        $valueDisplay = match ($type) {
            'string' => $this->formatString($var, $isHtml),
            'integer', 'double' => $this->formatNumber($var, $isHtml),
            'boolean' => $this->formatBoolean($var, $isHtml),
            'NULL' => $this->formatNull($isHtml),
            'array' => $this->formatArray($var, $isHtml, $depth),
            'object' => $this->formatObject($var, $isHtml, $depth),
            'resource' => $this->formatResource($var, $isHtml),
            default => "($type)"
        };

        $typeColor = $this->getTypeColor($type, $isHtml);
        $labelOutput = $label ? $this->colorText($label . ' ', 'label', $isHtml) : '';

        echo $indent . $labelOutput . $this->colorText("($type)", $typeColor, $isHtml) . ' ' . $valueDisplay . PHP_EOL;
    }

    /**
     * Format string for output
     */
    private function formatString(string $var, bool $isHtml): string
    {
        $length = strlen($var);

        // Handle multiline strings
        if (str_contains($var, "\n")) {
            $lines = explode("\n", $var);
            $firstLine = htmlspecialchars($lines[0], ENT_QUOTES | ENT_HTML5);
            $result = $this->colorText('"' . $firstLine, 'string', $isHtml);

            // Indent and append remaining lines
            for ($i = 1; $i < count($lines); $i++) {
                $line = htmlspecialchars($lines[$i], ENT_QUOTES | ENT_HTML5);
                $result .= "\n" . str_repeat(' ', 8) . $this->colorText($line, 'string', $isHtml);
            }

            $result .= $this->colorText('"', 'string', $isHtml) .
                $this->colorText(" (length=" . $length . ", multiline)", 'meta', $isHtml);

            return $result;
        }

        // Handle regular strings
        $var = htmlspecialchars($var, ENT_QUOTES | ENT_HTML5);

        if ($length > $this->maxStrLength) {
            $var = substr($var, 0, $this->maxStrLength) . '...';
        }

        return $this->colorText('"' . $var . '"', 'string', $isHtml) .
            $this->colorText(" (length=" . $length . ")", 'meta', $isHtml);
    }


    /**
     * Format numeric value for output
     */
    private function formatNumber(int|float $var, bool $isHtml): string
    {
        return $this->colorText((string)$var, 'number', $isHtml);
    }

    /**
     * Format boolean for output
     */
    private function formatBoolean(bool $var, bool $isHtml): string
    {
        return $this->colorText($var ? 'true' : 'false', 'boolean', $isHtml);
    }

    /**
     * Format null for output
     */
    private function formatNull(bool $isHtml): string
    {
        return $this->colorText('null', 'null', $isHtml);
    }

    /**
     * Format array for output
     */
    private function formatArray(array $var, bool $isHtml, int $depth): string
    {
        $count = count($var);
        if ($depth >= $this->maxDepth) {
            return $this->colorText("Array", 'array', $isHtml) .
                $this->colorText(" (items=" . $count . ")", 'meta', $isHtml) .
                $this->colorText(" [MAX DEPTH REACHED]", 'error', $isHtml);
        }

        $result = $this->colorText("Array", 'array', $isHtml) .
            $this->colorText(" (items=" . $count . ")", 'meta', $isHtml) .
            " {" . PHP_EOL;

        $i = 0;
        foreach ($var as $key => $value) {
            if ($i >= $this->maxChildren) {
                $indent = str_repeat('    ', $depth + 1);
                $result .= $indent . $this->colorText("... +" . ($count - $this->maxChildren) . " more items", 'meta', $isHtml) . PHP_EOL;
                break;
            }

            $keyDisplay = is_string($key) ?
                $this->colorText("\"$key\"", 'key', $isHtml) :
                $this->colorText((string)$key, 'key', $isHtml);

            $result .= str_repeat('    ', $depth + 1) .
                "[" . $keyDisplay . "] => ";

            // Capture output from recursive call
            ob_start();
            $this->dumpVar($value, '', $isHtml, $depth + 1);
            $result .= trim(ob_get_clean());
            $result .= PHP_EOL;

            $i++;
        }

        $result .= str_repeat('    ', $depth) . "}";
        return $result;
    }

    /**
     * Format object for output
     */
    private function formatObject(object $var, bool $isHtml, int $depth): string
    {
        $class = get_class($var);
        $props = (array)$var;
        $count = count($props);

        if ($depth >= $this->maxDepth) {
            return $this->colorText($class, 'object', $isHtml) .
                $this->colorText(" (properties=" . $count . ")", 'meta', $isHtml) .
                $this->colorText(" [MAX DEPTH REACHED]", 'error', $isHtml);
        }

        $result = $this->colorText($class, 'object', $isHtml) .
            $this->colorText(" (properties=" . $count . ")", 'meta', $isHtml) .
            " {" . PHP_EOL;

        $i = 0;
        foreach ($props as $key => $value) {
            if ($i >= $this->maxChildren) {
                $indent = str_repeat('    ', $depth + 1);
                $result .= $indent . $this->colorText("... +" . ($count - $this->maxChildren) . " more properties", 'meta', $isHtml) . PHP_EOL;
                break;
            }

            // Handle property name with potential visibility indicator
            // Fix: Ensure $key is a string before passing to explode
            $keyString = (string)$key;
            $keyParts = explode("\0", $keyString);
            $propName = end($keyParts);
            $visibility = 'public';

            if (count($keyParts) > 1) {
                $visibility = $keyParts[1] === '*' ? 'protected' : 'private';
            }

            $visColor = match ($visibility) {
                'private' => 'private',
                'protected' => 'protected',
                default => 'public'
            };

            $result .= str_repeat('    ', $depth + 1) .
                $this->colorText("[$visibility]", $visColor, $isHtml) . " " .
                $this->colorText("$propName", 'key', $isHtml) . " => ";

            // Capture output from recursive call
            ob_start();
            $this->dumpVar($value, '', $isHtml, $depth + 1);
            $result .= trim(ob_get_clean());
            $result .= PHP_EOL;

            $i++;
        }

        $result .= str_repeat('    ', $depth) . "}";
        return $result;
    }

    /**
     * Format resource for output
     */
    private function formatResource($var, bool $isHtml): string
    {
        $type = get_resource_type($var);
        return $this->colorText("resource($type)", 'resource', $isHtml) .
            $this->colorText(" id=" . (int)$var, 'meta', $isHtml);
    }

    /**
     * Get color associated with type
     */
    private function getTypeColor(string $type, bool $isHtml): string
    {
        return match ($type) {
            'string' => 'string',
            'integer', 'double' => 'number',
            'boolean' => 'boolean',
            'NULL' => 'null',
            'array' => 'array',
            'object' => 'object',
            'resource' => 'resource',
            default => 'default'
        };
    }

    /**
     * Apply color to text based on context
     */
    private function colorText(string $text, string $context, bool $isHtml): string
    {
        if (!$isHtml) {
            // ANSI color codes for CLI
            return match ($context) {
                'string' => "\033[0;32m" . $text . "\033[0m", // Green
                'number' => "\033[0;34m" . $text . "\033[0m", // Blue
                'boolean' => "\033[0;35m" . $text . "\033[0m", // Magenta
                'null' => "\033[0;31m" . $text . "\033[0m", // Red
                'array' => "\033[0;33m" . $text . "\033[0m", // Yellow
                'object' => "\033[0;36m" . $text . "\033[0m", // Cyan
                'resource' => "\033[0;95m" . $text . "\033[0m", // Light magenta
                'key' => "\033[0;33m" . $text . "\033[0m", // Yellow
                'meta' => "\033[0;90m" . $text . "\033[0m", // Dark gray
                'error' => "\033[0;91m" . $text . "\033[0m", // Light red
                'label' => "\033[1;37m" . $text . "\033[0m", // Bold white
                'public' => "\033[0;92m" . $text . "\033[0m", // Light green
                'protected' => "\033[0;93m" . $text . "\033[0m", // Light yellow
                'private' => "\033[0;91m" . $text . "\033[0m", // Light red
                default => $text
            };
        }

        // HTML colors
        return match ($context) {
            'string' => '<span style="color:#a5d6a7;">' . $text . '</span>',
            'number' => '<span style="color:#90caf9;">' . $text . '</span>',
            'boolean' => '<span style="color:#ce93d8;">' . $text . '</span>',
            'null' => '<span style="color:#ef9a9a;">' . $text . '</span>',
            'array' => '<span style="color:#ffcc80;">' . $text . '</span>',
            'object' => '<span style="color:#80deea;">' . $text . '</span>',
            'resource' => '<span style="color:#ea80fc;">' . $text . '</span>',
            'key' => '<span style="color:#ffe082;">' . $text . '</span>',
            'meta' => '<span style="color:#9e9e9e;">' . $text . '</span>',
            'error' => '<span style="color:#ef5350;">' . $text . '</span>',
            'label' => '<span style="color:#ffffff;font-weight:bold;">' . $text . '</span>',
            'public' => '<span style="color:#81c784;">' . $text . '</span>',
            'protected' => '<span style="color:#fff176;">' . $text . '</span>',
            'private' => '<span style="color:#ef5350;">' . $text . '</span>',
            default => $text
        };
    }
}