<?php

declare(strict_types=1);

namespace Catalyst\Framework\Core\Database;

use PDO;
use PDOException;

/**
 * Database connection tester
 *
 * Provides methods for testing database connections without establishing
 * persistent connections in the connection pool.
 *
 * @package Catalyst\Framework\Core\Database
 */
class ConnectionTester
{
    /**
     * Test a database connection with provided credentials
     *
     * @param string $host
     * @param int $port
     * @param string $database
     * @param string $username
     * @param string $password
     * @return array Result with success status and message
     */
    public static function test(
        string $host,
        int    $port,
        string $database,
        string $username,
        string $password
    ): array
    {
        try {
            // Create DSN
            $dsn = "mysql:host=$host;port=$port;dbname=$database";

            // Define options for PDO
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            // Attempt to create a new PDO instance
            $pdo = new PDO($dsn, $username, $password, $options);

            // Test query
            $stmt = $pdo->query('SELECT 1');

            // Connection successful
            return [
                'success' => true,
                'message' => "Successfully connected to database '$database' on '$host:$port'"
            ];
        } catch (PDOException $e) {
            // Connection failed
            return [
                'success' => false,
                'message' => "Database connection failed: " . $e->getMessage()
            ];
        }
    }
}
