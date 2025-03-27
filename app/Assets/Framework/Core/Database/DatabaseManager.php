<?php

declare(strict_types=1);

namespace Catalyst\Framework\Core\Database;


use Catalyst\Assets\Framework\Core\Exceptions\ConnectionException;
use Catalyst\Framework\Traits\SingletonTrait;
use Catalyst\Helpers\Log\Logger;
use Exception;

/**
 * Database connection manager
 *
 * Manages database connections and provides a central access point for database operations.
 * Uses singleton pattern to ensure only one manager instance exists application-wide.
 *
 * @package Catalyst\Framework\Core\Database
 */
class DatabaseManager
{
    use SingletonTrait;

    /**
     * Active database connections
     *
     * @var array<string, Connection>
     */
    protected array $connections = [];

    /**
     * Connection configurations
     *
     * @var array
     */
    protected array $configs = [];

    /**
     * Logger instance
     *
     * @var Logger
     */
    protected Logger $logger;

    /**
     * Default connection name
     *
     * @var string|null
     */
    protected ?string $defaultConnection = null;

    /**
     * Protected constructor for singleton pattern
     * @throws Exception
     */
    protected function __construct()
    {
        $this->logger = Logger::getInstance();
        $this->loadConfigurations();
    }

    /**
     * Load database configurations from config files
     *
     * @return void
     * @throws Exception
     */
    protected function loadConfigurations(): void
    {
        try {
            $environment = getenv('APP_ENV') ?: 'development';
            $configPath = implode(DS, [PD, 'bootstrap', 'config', $environment, 'db.json']);

            if (file_exists($configPath)) {
                $content = file_get_contents($configPath);
                $configs = json_decode($content, true);

                if (is_array($configs)) {
                    $this->configs = $configs;
                    // Set the first connection as default if not already set
                    if (empty($this->defaultConnection) && !empty(array_keys($configs))) {
                        $this->defaultConnection = array_keys($configs)[0];
                    }
                }
            }
        } catch (Exception $e) {
            $this->logger->error('Failed to load database configurations', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get a database connection by name
     *
     * @param string|null $name Connection name or null for default
     * @return Connection
     * @throws ConnectionException If connection configuration doesn't exist
     */
    public function connection(?string $name = null): Connection
    {
        $connectionName = $name ?: $this->defaultConnection;

        if (empty($connectionName)) {
            throw new ConnectionException('No default database connection configured');
        }

        // Return existing connection if available
        if (isset($this->connections[$connectionName])) {
            return $this->connections[$connectionName];
        }

        // Create new connection
        if (!isset($this->configs[$connectionName])) {
            throw new ConnectionException("Database configuration for '$connectionName' not found");
        }

        $config = $this->configs[$connectionName];
        $connection = new Connection(
            $config['db_host'] ?? 'localhost',
            $config['db_port'] ?? 3306,
            $config['db_name'] ?? '',
            $config['db_user'] ?? '',
            $config['db_password'] ?? '',
            $connectionName
        );

        // Store for reuse
        $this->connections[$connectionName] = $connection;

        return $connection;
    }

    /**
     * Set the default connection name
     *
     * @param string $name Connection name
     * @return self
     */
    public function setDefaultConnection(string $name): self
    {
        $this->defaultConnection = $name;
        return $this;
    }

    /**
     * Get all available connection names
     *
     * @return array<string>
     */
    public function getConnectionNames(): array
    {
        return array_keys($this->configs);
    }

    /**
     * Check if a connection exists by name
     *
     * @param string $name Connection name
     * @return bool
     */
    public function hasConnection(string $name): bool
    {
        return isset($this->configs[$name]);
    }

    /**
     * Get query builder for the specified connection
     *
     * @param string $tableName
     * @param string|null $connection Connection name or null for default
     * @return QueryBuilder
     */
    public function table(string $tableName, ?string $connection = null): QueryBuilder
    {
        return $this->connection($connection)->table($tableName);
    }
}
