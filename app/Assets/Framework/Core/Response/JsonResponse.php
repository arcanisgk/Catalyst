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

namespace App\Assets\Framework\Core\Response;

use InvalidArgumentException;
use JsonException;

/**************************************************************************************
 * JsonResponse class for JSON API responses
 *
 * Specializes the base Response class for JSON content with appropriate
 * content-type headers and JSON encoding.
 *
 * @package App\Assets\Framework\Core\Response;
 */
class JsonResponse extends Response
{
    /**
     * JSON encoding options
     *
     * @var int
     */
    protected int $encodingOptions;

    /**
     * Original data before JSON encoding
     *
     * @var mixed
     */
    protected mixed $data;

    /**
     * Create a new JSON response
     *
     * @param mixed $data The data to encode as JSON
     * @param int $status The HTTP status code
     * @param array $headers Array of HTTP headers
     * @param int $options JSON encoding options
     * @param bool $json Whether the data is already JSON encoded
     * @throws InvalidArgumentException
     */
    public function __construct(
        mixed $data = null,
        int   $status = 200,
        array $headers = [],
        int   $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        bool  $json = false
    )
    {
        $this->encodingOptions = $options;

        // Set JSON-specific headers
        $headers['Content-Type'] = $headers['Content-Type'] ?? 'application/json';

        if ($json && is_string($data)) {
            // Data is already JSON encoded
            $this->data = json_decode($data, true);
            parent::__construct($data, $status, $headers);
        } else {
            // Data needs encoding
            $this->data = $data;
            parent::__construct($this->encodeData($data), $status, $headers);
        }
    }

    /**
     * Get the original data
     *
     * @return mixed Original data
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * Set the data and encode it as JSON
     *
     * @param mixed $data The data to encode
     * @return self For method chaining
     */
    public function setData(mixed $data): self
    {
        $this->data = $data;
        $this->setContent($this->encodeData($data));

        return $this;
    }

    /**
     * Set JSON encoding options
     *
     * @param int $options JSON encoding options
     * @return self For method chaining
     */
    public function setEncodingOptions(int $options): self
    {
        $this->encodingOptions = $options;
        $this->setContent($this->encodeData($this->data));

        return $this;
    }

    /**
     * Get current JSON encoding options
     *
     * @return int JSON encoding options
     */
    public function getEncodingOptions(): int
    {
        return $this->encodingOptions;
    }

    /**
     * Encode the given data as JSON
     *
     * @param mixed $data Data to encode
     * @return string JSON encoded string
     * @throws InvalidArgumentException If encoding fails
     */
    protected function encodeData(mixed $data): string
    {
        if ($data === null) {
            return 'null';
        }

        try {
            $json = json_encode($data, $this->encodingOptions | JSON_THROW_ON_ERROR);

            if ($json === false) {
                throw new InvalidArgumentException('JSON encoding failed');
            }

            return $json;
        } catch (JsonException $e) {
            throw new InvalidArgumentException(
                'JSON encoding error: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Create a JSON response for an API result
     *
     * @param mixed $data The data payload
     * @param bool $success Whether the API call was successful
     * @param string|null $message Optional message
     * @param int $status HTTP status code
     * @param array $headers HTTP headers
     * @return self New JsonResponse instance
     */
    public static function api(
        mixed   $data = null,
        bool    $success = true,
        ?string $message = null,
        int     $status = 200,
        array   $headers = []
    ): self
    {
        $result = [
            'success' => $success,
            'data' => $data
        ];

        if ($message !== null) {
            $result['message'] = $message;
        }

        return new self($result, $status, $headers);
    }

    /**
     * Create a JSON error response
     *
     * @param string $message Error message
     * @param mixed $errors Detailed error information
     * @param int $status HTTP status code
     * @param array $headers HTTP headers
     * @return self New JsonResponse instance
     */
    public static function error(
        string $message,
        mixed  $errors = null,
        int    $status = 400,
        array  $headers = []
    ): self
    {
        $data = [
            'success' => false,
            'message' => $message
        ];

        if ($errors !== null) {
            $data['errors'] = $errors;
        }

        return new self($data, $status, $headers);
    }

    /**
     * Create a JSON validation error response
     *
     * @param array $errors Validation errors
     * @param string $message Error message
     * @param int $status HTTP status code
     * @param array $headers HTTP headers
     * @return self New JsonResponse instance
     */
    public static function validation(
        array  $errors,
        string $message = 'Validation failed',
        int    $status = 422,
        array  $headers = []
    ): self
    {
        return self::error($message, $errors, $status, $headers);
    }
}
