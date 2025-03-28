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

namespace Catalyst\Framework\Core\Database;


use Catalyst\Framework\Core\Exceptions\ConnectionException;
use Catalyst\Framework\Core\Exceptions\QueryException;
use Catalyst\Helpers\Log\Logger;
use Closure;
use Exception;
use PDO;
use PDOException;
use PDOStatement;

/**************************************************************************************
 * Database connection
 *
 * Encapsulates PDO connection and provides methods for executing
 * queries and managing transactions.
 *
 * @package Catalyst\Framework\Core\Database
 */
class Connection
{
    /**
     * PDO connection instance
     *
     * @var PDO|null
     */
    protected ?PDO $pdo = null;

    /**
     * Logger instance
     *
     * @var Logger
     */
    protected Logger $logger;

    /**
     * Connection name
     *
     * @var string
     */
    protected string $name;

    /**
     * Connection parameters
     *
     * @var array
     */
    protected array $params;

    /**
     * Connection constructor
     *
     * @param string $host Database host
     * @param int $port Database port
     * @param string $database Database name
     * @param string $username Database username
     * @param string $password Database password
     * @param string $name Connection name
     */
    public function __construct(
        string $host,
        int    $port,
        string $database,
        string $username,
        string $password,
        string $name
    )
    {
        $this->logger = Logger::getInstance();
        $this->name = $name;
        $this->params = [
            'host' => $host,
            'port' => $port,
            'database' => $database,
            'username' => $username,
            'password' => $password
        ];
    }

    /**
     * Get the PDO instance, connecting if necessary
     *
     * @return PDO
     * @throws ConnectionException|Exception
     */
    public function getPdo(): PDO
    {
        if ($this->pdo === null) {
            $this->connect();
        }

        return $this->pdo;
    }

    /**
     * Connect to the database
     *
     * @return void
     * @throws ConnectionException|Exception
     */
    protected function connect(): void
    {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $this->params['host'],
                $this->params['port'],
                $this->params['database']
            );

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'
            ];

            $this->pdo = new PDO(
                $dsn,
                $this->params['username'],
                $this->params['password'],
                $options
            );

            $this->logger->debug("Database connection '$this->name' established");
        } catch (PDOException $e) {
            $this->logger->error("Database connection '$this->name' failed", [
                'error' => $e->getMessage()
            ]);

            throw new ConnectionException(
                "Failed to connect to database: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Create a new query builder
     *
     * @param string $table Table name
     * @return QueryBuilder
     */
    public function table(string $table): QueryBuilder
    {
        return new QueryBuilder($this, $table);
    }

    /**
     * Execute a SQL query with parameters
     *
     * @param string $query SQL query with placeholders
     * @param array $params Parameters for the query
     * @return PDOStatement
     * @throws QueryException|Exception
     */
    public function query(string $query, array $params = []): PDOStatement
    {
        try {
            $statement = $this->getPdo()->prepare($query);
            $statement->execute($params);
            return $statement;
        } catch (PDOException $e) {
            $this->logger->error("Query execution failed", [
                'connection' => $this->name,
                'query' => $query,
                'params' => $params,
                'error' => $e->getMessage()
            ]);

            throw new QueryException(
                "Query execution failed: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Execute a SQL query and fetch all results
     *
     * @param string $query SQL query with placeholders
     * @param array $params Parameters for the query
     * @param int $fetchMode PDO fetch mode
     * @return array
     * @throws QueryException|Exception
     */
    public function select(string $query, array $params = [], int $fetchMode = PDO::FETCH_ASSOC): array
    {
        return $this->query($query, $params)->fetchAll($fetchMode);
    }

    /**
     * Execute a SQL query and fetch a single row
     *
     * @param string $query SQL query with placeholders
     * @param array $params Parameters for the query
     * @param int $fetchMode PDO fetch mode
     * @return array|null Row data or null if not found
     * @throws QueryException|Exception
     */
    public function selectOne(string $query, array $params = [], int $fetchMode = PDO::FETCH_ASSOC): ?array
    {
        $result = $this->query($query, $params)->fetch($fetchMode);
        return $result !== false ? $result : null;
    }

    /**
     * Execute a non-SELECT SQL statement
     *
     * @param string $query SQL query with placeholders
     * @param array $params Parameters for the query
     * @return int Number of affected rows
     * @throws QueryException|Exception
     */
    public function execute(string $query, array $params = []): int
    {
        return $this->query($query, $params)->rowCount();
    }

    /**
     * Insert a row into a table
     *
     * @param string $table Table name
     * @param array $data Column-value pairs
     * @return int Last insert ID
     * @throws QueryException|Exception
     */
    public function insert(string $table, array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":$col", $columns);

        $query = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $params = [];
        foreach ($data as $column => $value) {
            $params[":$column"] = $value;
        }

        $this->query($query, $params);
        return (int)$this->getPdo()->lastInsertId();
    }

    /**
     * Execute a function within a transaction
     *
     * @param Closure $callback Function to execute
     * @return mixed The return value of the callback
     * @throws QueryException|Exception
     */
    public function transaction(Closure $callback): mixed
    {
        try {
            $this->getPdo()->beginTransaction();
            $this->logger->debug("Transaction started on '$this->name'");

            $result = $callback($this);

            $this->getPdo()->commit();
            $this->logger->debug("Transaction committed on '$this->name'");

            return $result;
        } catch (QueryException $e) {
            $this->rollback();
            throw $e;
        } catch (Exception $e) {
            $this->rollback();
            throw new QueryException(
                "Transaction failed: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Roll back the current transaction
     *
     * @return void
     * @throws Exception
     */
    protected function rollback(): void
    {
        if ($this->pdo !== null && $this->pdo->inTransaction()) {
            $this->pdo->rollBack();
            $this->logger->debug("Transaction rolled back on '$this->name'");
        }
    }

    /**
     * Test the connection
     *
     * @return bool True if connection is successful
     * @throws Exception
     */
    public function test(): bool
    {
        try {
            $this->getPdo()->query('SELECT 1');
            return true;
        } catch (ConnectionException|PDOException $e) {
            return false;
        }
    }

    /**
     * Get connection information
     *
     * @return array Connection parameters (with password masked)
     */
    public function getConnectionInfo(): array
    {
        $info = $this->params;
        // Mask password for security
        $info['password'] = '********';
        return $info;
    }

    /**
     * Get connection name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}