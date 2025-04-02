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
 * DrawBox component for the Catalyst Framework
 *
 */

namespace Catalyst\Helpers\ToolBox;

use Catalyst\Framework\Traits\SingletonTrait;
use Catalyst\Helpers\IO\FileOutput;
use Exception;
use function mb_strlen;

/**************************************************************************************
 * DrawBox class for creating formatted text boxes in terminal or HTML
 *
 * @package Catalyst\Helpers\ToolBox;
 */
class DrawBox
{
    use SingletonTrait;

    /**
     * @var array Box characters for drawing
     */
    private array $boxChars = [
        'tl' => '╔', // top left
        'tr' => '╗', // top right
        'bl' => '╚', // bottom left
        'br' => '╝', // bottom right
        'v' => '║', // vertical line
        'h' => '═', // horizontal line
        'hs' => '─', // horizontal separator line
        'ls' => '╟', // left separator
        'rs' => '╢', // right separator
    ];

    /**
     * DrawBox constructor
     */
    protected function __construct()
    {
        // Constructor is protected due to SingletonTrait usage
    }


    /**
     * Draw a box around the given content
     *
     * @param array|string $content Content to place inside the box
     * @param array $options Box options and styling
     * @return string Formatted box as string
     * @throws Exception
     */
    public function draw(array|string $content, array $options = []): string
    {
        // Default options
        $defaultOptions = [
            'headerLines' => 0,
            'footerLines' => 0,
            'highlight' => false,
            'maxWidth' => 0,
            'style' => 0,
            'isError' => false,
            'htmlOutput' => false,
            'enableFileOutput' => true  // New option to control file output functionality
        ];

        $options = array_merge($defaultOptions, $options);

        // Check if we should output HTML
        if ($options['htmlOutput'] || (!$this->isCli() && !defined('FORCE_CLI_OUTPUT'))) {
            return $this->drawHtml($content, $options);
        }

        $boxOutput = $this->drawCli($content, $options);

        // Handle file output if enabled in options
        if ($options['enableFileOutput'] && !$options['isError']) {
            $fileService = FileOutput::getInstance();

            if ($fileService->isFileOutputRequested()) {
                $result = $fileService->handleFileOutput($boxOutput);

                // Append file operation result to the box
                if ($result['success'] || $result['filename']) {
                    $boxOutput = $this->appendFileOperationResult($boxOutput, $result);
                }
            }
        }

        return $boxOutput;
    }

    /**
     * Append file operation result to box output
     *
     * @param string $boxOutput Original box output
     * @param array $result File operation result
     * @return string Modified box output
     */
    private function appendFileOperationResult(string $boxOutput, array $result): string
    {
        // Extract the bottom line
        $lines = explode(PHP_EOL, $boxOutput);
        $bottomLine = array_pop($lines);

        // Calculate separator and status lines
        $printArea = mb_strlen($lines[0] ?? '') - 2;
        $separatorLine = $this->boxChars['ls'] . str_repeat($this->boxChars['hs'], $printArea) . $this->boxChars['rs'];
        $statusLine = str_pad($result['message'], $printArea, ' ', STR_PAD_BOTH);

        // Get color codes from the existing output
        $colorCode = '';
        $resetCode = '';
        if (preg_match('/(\x1B\[\d+(?:;\d+)*m)/', $boxOutput, $colorMatches)) {
            $colorCode = $colorMatches[1] ?? '';
            if (preg_match('/(\x1B\[0m)/', $boxOutput, $resetMatches)) {
                $resetCode = $resetMatches[1] ?? '';
            }
        }

        // Reconstruct the output with file status
        $newLines = implode(PHP_EOL, $lines);
        $fileStatusInfo = PHP_EOL . $colorCode . $separatorLine . $resetCode . PHP_EOL .
            $colorCode . $this->boxChars['v'] . $resetCode . $statusLine .
            $colorCode . $this->boxChars['v'] . $resetCode . PHP_EOL;

        return $newLines . $fileStatusInfo . $bottomLine;
    }

    /**
     * Draw a box in CLI mode
     *
     * @param array|string $content Content to place inside the box
     * @param array $options Box options and styling
     * @return string Formatted CLI box
     * @throws Exception
     */
    private function drawCli(array|string $content, array $options): string
    {
        $content = is_array($content) ? $content : preg_split('/\r\n|\r|\n/', rtrim($content));
        $colorScheme = $this->getColorScheme($options['style']);

        $maxContentWidth = $this->getMaxContentWidth($content);
        $termWidth = $this->getTerminalWidth();

        // Calculate box width
        $boxWidth = $this->calculateBoxWidth($maxContentWidth, $options['maxWidth'], $termWidth);

        // Check if terminal is wide enough
        $fileService = FileOutput::getInstance();
        if (!$this->isTerminalWideEnough($boxWidth, $termWidth) && !$fileService->isFileOutputRequested()) {
            return $this->generateTerminalTooNarrowMessage($boxWidth, $termWidth);
        }

        // Build the box
        return $this->buildCliBox($content, $boxWidth, $options, $colorScheme);
    }

    /**
     * Generate HTML representation of a box
     *
     * @param array|string $content Content to place inside the box
     * @param array $options Box options and styling
     * @return string HTML formatted box
     */
    private function drawHtml(array|string $content, array $options): string
    {
        $content = is_array($content) ? $content : preg_split('/\r\n|\r|\n/', rtrim($content));
        $styleClass = $this->getHtmlStyleClass($options['style'], $options['isError']);

        $html = '<pre class="catalyst-box ' . $styleClass . '">';
        $html .= '<div class="box-content">';

        // Process header
        if ($options['headerLines'] > 0) {
            $html .= '<div class="box-header">';
            for ($i = 0; $i < $options['headerLines']; $i++) {
                $html .= htmlspecialchars($content[$i] ?? '') . "\n";
            }
            $html .= '</div>';
        }

        // Process main content
        $html .= '<div class="box-body">';
        $startIdx = $options['headerLines'];
        $endIdx = count($content) - $options['footerLines'];

        for ($i = $startIdx; $i < $endIdx; $i++) {
            $html .= htmlspecialchars($content[$i] ?? '') . "\n";
        }
        $html .= '</div>';

        // Process footer
        if ($options['footerLines'] > 0) {
            $html .= '<div class="box-footer">';
            for ($i = $endIdx; $i < count($content); $i++) {
                $html .= htmlspecialchars($content[$i] ?? '') . "\n";
            }
            $html .= '</div>';
        }

        $html .= '</div></pre>';

        return $html;
    }

    /**
     * Build a CLI box around content
     *
     * @param array $content Content array
     * @param int $boxWidth Width of the box
     * @param array $options Box options
     * @param array $colorScheme Color scheme for the box
     * @return string Formatted box
     */
    private function buildCliBox(array $content, int $boxWidth, array $options, array $colorScheme): string
    {
        $printArea = $boxWidth - 2;
        $highlight = $options['highlight'];
        $headerLines = $options['headerLines'];
        $footerLines = $options['footerLines'];

        $cliColors = [
            'hf' => ($highlight && $headerLines !== 0) ? "\033{$colorScheme['c']}" : '',
            'reset' => $highlight ? "\033{$colorScheme['r']}" : '',
        ];

        $topLine = $this->boxChars['tl'] . str_repeat($this->boxChars['h'], $printArea) . $this->boxChars['tr'];
        $bottomLine = $this->boxChars['bl'] . str_repeat($this->boxChars['h'], $printArea) . $this->boxChars['br'];
        $separatorLine = $this->boxChars['ls'] . str_repeat($this->boxChars['hs'], $printArea) . $this->boxChars['rs'];

        $lines = [];
        $lines[] = $cliColors['hf'] . $topLine . $cliColors['reset'] . PHP_EOL;

        // Process each line of content
        $this->processContentLines($content, $lines, $cliColors, $printArea, $headerLines, $footerLines, $separatorLine);

        $lines[] = $cliColors['hf'] . $bottomLine . $cliColors['reset'] . PHP_EOL;

        return implode('', $lines);
    }

    /**
     * Process content lines for CLI box
     *
     * @param array $content Content lines
     * @param array &$lines Output lines array
     * @param array $cliColors CLI color codes
     * @param int $printArea Width of print area
     * @param int $headerLines Number of header lines
     * @param int $footerLines Number of footer lines
     * @param string $separatorLine Separator line string
     * @return void
     */
    private function processContentLines(
        array  $content,
        array  &$lines,
        array  $cliColors,
        int    $printArea,
        int    $headerLines,
        int    $footerLines,
        string $separatorLine
    ): void
    {
        $totalLines = count($content);
        $start = true;

        for ($i = 0; $i < $totalLines; $i++) {
            $line = $content[$i];

            // Process header lines
            if ($headerLines > 0 && $i < $headerLines) {
                $paddedLine = str_pad($line, $printArea, ' ', STR_PAD_BOTH);
                $lines[] = $cliColors['hf'] . $this->boxChars['v'] . $paddedLine .
                    $this->boxChars['v'] . $cliColors['reset'] . PHP_EOL;

                if ($i == $headerLines - 1) {
                    $lines[] = $cliColors['hf'] . $separatorLine . $cliColors['reset'] . PHP_EOL;
                }
            } // Process footer lines
            elseif ($footerLines > 0 && $i >= ($totalLines - $footerLines)) {
                if ($i == $totalLines - $footerLines) {
                    $lines[] = $cliColors['hf'] . $separatorLine . $cliColors['reset'] . PHP_EOL;
                }

                $paddedLine = str_pad($line, $printArea, ' ', STR_PAD_BOTH);
                $lines[] = $cliColors['hf'] . $this->boxChars['v'] . $paddedLine .
                    $this->boxChars['v'] . $cliColors['reset'] . PHP_EOL;
            } // Process main content
            else {
                $fileService = FileOutput::getInstance();
                $plainLine = $fileService->removeAnsiSequences($line);

                if ($start) {
                    $lines[] = $cliColors['hf'] . $this->boxChars['v'] . $cliColors['reset'] .
                        str_pad('', $printArea) .
                        $cliColors['hf'] . $this->boxChars['v'] . $cliColors['reset'] . PHP_EOL;
                    $start = false;
                }

                if ($printArea >= mb_strlen($plainLine) && $plainLine !== '') {
                    $expoLine = explode($plainLine, str_pad($plainLine, $printArea));
                    $formattedLine = implode($line, $expoLine);
                    $lines[] = $cliColors['hf'] . $this->boxChars['v'] . $cliColors['reset'] .
                        $formattedLine . $cliColors['hf'] . $this->boxChars['v'] .
                        $cliColors['reset'] . PHP_EOL;
                } else {
                    $chunks = $this->splitLineToFit($line, $printArea - 2);

                    foreach ($chunks as $chunkLine) {
                        $chunkLine = str_pad($chunkLine, $printArea - 2);
                        $lines[] = $cliColors['hf'] . $this->boxChars['v'] . $cliColors['reset'] .
                            ' ' . $chunkLine . ' ' . $cliColors['hf'] . $this->boxChars['v'] .
                            $cliColors['reset'] . PHP_EOL;
                    }
                }

                if ($i === $totalLines - 1 || ($footerLines > 0 && $i === $totalLines - $footerLines - 1)) {
                    $lines[] = $cliColors['hf'] . $this->boxChars['v'] . $cliColors['reset'] .
                        str_pad('', $printArea) .
                        $cliColors['hf'] . $this->boxChars['v'] . $cliColors['reset'] . PHP_EOL;
                }
            }
        }
    }

    /**
     * Get maximum width of content lines
     *
     * @param array $content Content lines
     * @return int Maximum width
     */
    private function getMaxContentWidth(array $content): int
    {
        return max(array_map([$this, 'getStringLengthWithoutANSI'], $content));
    }

    /**
     * Calculate box width based on content and constraints
     *
     * @param int $contentWidth Content width
     * @param int $maxWidth Maximum allowed width
     * @param int $termWidth Terminal width
     * @return int Final box width
     */
    private function calculateBoxWidth(int $contentWidth, int $maxWidth, int $termWidth): int
    {
        if ($maxWidth === 0) {
            return $termWidth;
        }

        return ($contentWidth > $maxWidth) ? $contentWidth : $maxWidth;
    }

    /**
     * Check if terminal is wide enough for the box
     *
     * @param int $boxWidth Box width
     * @param int $termWidth Terminal width
     * @return bool True if terminal is wide enough
     */
    private function isTerminalWideEnough(int $boxWidth, int $termWidth): bool
    {
        return $boxWidth <= $termWidth;
    }

    /**
     * Generate error message for narrow terminal
     *
     * @param int $required Required width
     * @param int $actual Actual width
     * @return string Error message
     * @throws Exception
     */
    private function generateTerminalTooNarrowMessage(int $required, int $actual): string
    {
        $message = '!!!Your Terminal Windows is too Narrow. Resize It!!!' . PHP_EOL .
            '==> Minimum Expected: ' . $required . PHP_EOL .
            '==> Given Size:       ' . $actual . PHP_EOL . PHP_EOL .
            'If you cannot Resize the window;' . PHP_EOL .
            'You can Output the data to a file and avoid this error:' . PHP_EOL .
            'php script.php -f="filename"';

        // Recursively call draw() with error styling
        return $this->draw($message, [
            'headerLines' => 1,
            'footerLines' => 1,
            'highlight' => true,
            'style' => 2,
            'isError' => true,
            'enableFileOutput' => false  // Disable file output for error messages
        ]);
    }

    /**
     * Get terminal width
     *
     * @return int Terminal width
     */
    private function getTerminalWidth(): int
    {
        return defined('TW') ? TW : 80;
    }

    /**
     * Check if running in CLI environment
     *
     * @return bool True if in CLI
     */
    private function isCli(): bool
    {
        return defined('IS_CLI') ? IS_CLI : (
            defined('STDIN')
            || php_sapi_name() === "cli"
            || (stristr(PHP_SAPI, 'cgi') && getenv('TERM'))
            || (empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv'] ?? []) > 0)
        );
    }

    /**
     * Get string length without ANSI codes
     *
     * @param string $string String with ANSI codes
     * @return int Length without ANSI codes
     */
    private function getStringLengthWithoutANSI(string $string): int
    {
        $fileService = FileOutput::getInstance();
        return mb_strlen($fileService->removeAnsiSequences($string));
    }

    /**
     * Split line to fit within given width
     *
     * @param string $line Line to split
     * @param int $maxWidth Maximum width
     * @return array Array of line chunks
     */
    private function splitLineToFit(string $line, int $maxWidth): array
    {
        $fileService = FileOutput::getInstance();
        $plainLine = $fileService->removeAnsiSequences($line);
        $delimiter = str_contains($plainLine, '=>') ? ' =>' : (str_contains($plainLine, ':') ? ':' : null);

        if ($delimiter === null || mb_strlen($plainLine) <= $maxWidth) {
            return $this->splitTextToChunks($line, $maxWidth);
        }

        // Handle lines with delimiters (key-value pairs)
        $parts = explode($delimiter, $line, 2);
        $plainParts = explode($delimiter, $plainLine, 2);

        $keyPart = $parts[0];
        $valuePart = $parts[1] ?? '';
        $keyLength = mb_strlen($plainParts[0]);
        $delimiterLength = mb_strlen($delimiter);

        // Calculate space for value part
        $valueWidth = $maxWidth - $keyLength - $delimiterLength;

        if ($valueWidth < 10) {
            // If key is too long, split the whole line instead
            return $this->splitTextToChunks($line, $maxWidth);
        }

        // Split the value part into chunks
        $valueChunks = $this->splitTextToChunks($valuePart, $valueWidth);

        $result = [];
        foreach ($valueChunks as $index => $chunk) {
            if ($index === 0) {
                // First line has key + delimiter + chunk
                $result[] = $keyPart . $delimiter . $chunk;
            } else {
                // Following lines have padding + chunk
                $padding = str_repeat(' ', $keyLength + $delimiterLength);
                $result[] = $padding . $chunk;
            }
        }

        return $result;
    }

    /**
     * Split text into chunks of maximum width
     *
     * @param string $text Text to split
     * @param int $maxWidth Maximum width
     * @return array Array of text chunks
     */
    private function splitTextToChunks(string $text, int $maxWidth): array
    {
        if ($maxWidth < 5) {
            $maxWidth = 5; // Minimum reasonable width
        }

        $fileService = FileOutput::getInstance();
        $plainText = $fileService->removeAnsiSequences($text);

        if (mb_strlen($plainText) <= $maxWidth) {
            return [$text];
        }

        // Extract ANSI color codes
        $ansiCodes = $this->extractAnsiCodes($text);
        $startCode = $ansiCodes['start'] ?? '';
        $resetCode = $ansiCodes['reset'] ?? '';

        // Split plain text into chunks
        $chunks = [];
        $textLength = mb_strlen($plainText);

        for ($i = 0; $i < $textLength; $i += $maxWidth) {
            $chunks[] = mb_substr($plainText, $i, $maxWidth);
        }

        // Add ANSI codes to each chunk if they exist
        if ($startCode && $resetCode) {
            $chunks = array_map(function ($chunk) use ($startCode, $resetCode) {
                return $startCode . $chunk . $resetCode;
            }, $chunks);
        }

        return $chunks;
    }

    /**
     * Extract ANSI color codes from string
     *
     * @param string $string String with ANSI codes
     * @return array Array with start and reset codes
     */
    private function extractAnsiCodes(string $string): array
    {
        $startPattern = '/\033\[(\d+(;\d+)*)m/';
        $resetPattern = '/\033\[0m/';

        preg_match($startPattern, $string, $startMatch);
        preg_match_all($resetPattern, $string, $resetMatches);

        return [
            'start' => $startMatch[0] ?? null,
            'reset' => end($resetMatches[0]) ?: null,
        ];
    }

    /**
     * Get color scheme for CLI output
     *
     * @param int $styleType Style type index
     * @return array Color scheme codes
     */
    private function getColorScheme(int $styleType): array
    {
        $colorScheme = ['r' => '[0m'];

        $colorScheme['c'] = match ($styleType) {
            1 => '[1;42;30m', // Success (green background)
            2 => '[1;41m',    // Error (red background)
            3 => '[1;43;30m', // Warning (yellow background)
            4 => '[1;44;30m', // Info (blue background)
            5 => '[1;32m',    // Success text (green)
            6 => '[1;31m',    // Error text (red)
            7 => '[1;46;30m', // Info alt (cyan background)
            8 => '[1;37m',    // Normal highlight
            9 => '[1;45m',    // Special (magenta background)
            default => '[0m', // Default
        };

        // Apply environment-based styling if no specific style
        if ($styleType === 0) {
            $envStyle = $this->getEnvironmentBasedStyle();
            if ($envStyle['color'] > 0) {
                $colorScheme['c'] = match ($envStyle['color']) {
                    1 => '[1;42;30m', // Dev
                    2 => '[1;44;30m', // Prod
                    default => '[0m',
                };
            }
        }

        return $colorScheme;
    }

    /**
     * Get HTML style class based on style type
     *
     * @param int $styleType Style type index
     * @param bool $isError Whether this is an error message
     * @return string CSS class name
     */
    private function getHtmlStyleClass(int $styleType, bool $isError): string
    {
        if ($isError) {
            return 'error-box';
        }

        return match ($styleType) {
            1 => 'success-box',
            2 => 'error-box',
            3 => 'warning-box',
            4 => 'info-box',
            5 => 'success-text-box',
            6 => 'error-text-box',
            7 => 'info-alt-box',
            8 => 'highlight-box',
            9 => 'special-box',
            default => 'default-box',
        };
    }

    /**
     * Get environment-based style
     *
     * @return array Environment style info
     */
    private function getEnvironmentBasedStyle(): array
    {
        if (defined('IS_DEVELOPMENT') && IS_DEVELOPMENT) {
            return ['color' => 1, 'label' => 'DEVELOPMENT'];
        } elseif (defined('IS_PRODUCTION') && IS_PRODUCTION) {
            return ['color' => 2, 'label' => 'PRODUCTION'];
        }

        return ['color' => 0, 'label' => ''];
    }
}