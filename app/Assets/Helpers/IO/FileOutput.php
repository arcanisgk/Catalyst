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

namespace App\Assets\Helpers\IO;

use Catalyst\Framework\Core\Argument;
use Catalyst\Framework\Exceptions\FileSystemException;
use Catalyst\Framework\Traits\SingletonTrait;
use App\Assets\Helpers\Log\Logger;
use Exception;

/**************************************************************************************
 * Service for handling file output operations
 *
 * @package App\Assets\Helpers\IO;
 */
class FileOutput
{
    use SingletonTrait;

    /**
     * Check if file output has been requested via command line argument
     *
     * @return bool True if file output is requested
     */
    public function isFileOutputRequested(): bool
    {
        $arguments = Argument::getArguments();
        return isset($arguments['f']);
    }

    /**
     * Get the requested output filename
     *
     * @return string|null Filename or null if not specified
     */
    public function getOutputFilename(): ?string
    {
        $arguments = Argument::getArguments();
        return $arguments['f'] ?? null;
    }

    /**
     * Write content to a file with proper exception handling
     *
     * @param string $filename Filename to write to
     * @param string $content Content to write
     * @return bool True if successful
     * @throws FileSystemException If file cannot be written
     */
    public function writeToFile(string $filename, string $content): bool
    {
        try {
            // Ensure directory exists
            $directory = dirname($filename);
            if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
                throw new FileSystemException(
                    "Unable to create directory '$directory' for output file"
                );
            }

            // Check if we can write to this location
            if (file_exists($filename) && !is_writable($filename)) {
                throw new FileSystemException(
                    "Output file '$filename' exists but is not writable"
                );
            }

            $result = file_put_contents($filename, $content);

            if ($result === false) {
                throw new FileSystemException(
                    "Failed to write content to file '$filename'"
                );
            }

            return true;
        } catch (FileSystemException $e) {
            // Already our custom exception, no need to wrap
            throw $e;
        } catch (Exception $e) {
            // Wrap unexpected exceptions
            throw new FileSystemException(
                "Unexpected error writing to file '$filename': " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Remove ANSI color codes from string
     *
     * @param string $text Text with ANSI codes
     * @return string Text without ANSI codes
     */
    public function removeAnsiSequences(string $text): string
    {
        $pattern = "#\x1B(?:[@-Z\\-_]|\[[0-?]*[ -/]*[@-~])#";
        return preg_replace($pattern, '', $text);
    }

    /**
     * Handle file output for content
     *
     * @param string $content Content to write to file
     * @return array{success: bool, filename: string, message: string} Result information
     * @throws Exception
     */
    public function handleFileOutput(string $content): array
    {
        $filename = $this->getOutputFilename();

        if (!$filename) {
            return [
                'success' => false,
                'filename' => '',
                'message' => 'No output filename specified'
            ];
        }

        // Remove ANSI sequences for clean file output
        $contentNoANSI = $this->removeAnsiSequences($content);

        try {
            $success = $this->writeToFile($filename, $contentNoANSI);

            return [
                'success' => $success,
                'filename' => $filename,
                'message' => "File '$filename' successfully created."
            ];
        } catch (FileSystemException $e) {
            // Log the error if Logger is available
            if (class_exists('\\App\\Assets\\Helpers\\Log\\Logger')) {
                Logger::getInstance()->error(
                    "File output error: " . $e->getMessage(),
                    ['filename' => $filename]
                );
            }

            return [
                'success' => false,
                'filename' => $filename,
                'message' => "Error creating file '$filename': " . $e->getMessage()
            ];
        }
    }
}