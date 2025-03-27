<?php

declare(strict_types=1);

namespace Catalyst\Framework\Core\Database;


use Catalyst\Assets\Framework\Core\Exceptions\QueryException;
use Catalyst\Helpers\Log\Logger;
use Exception;

/**
 * Database transaction handler
 *
 * Provides methods for managing database transactions with clean syntax.
 *
 * @package Catalyst\Framework\Core\Database
 */
class Transaction
{
    /**
     * Logger instance
     *
     * @var Logger
     */
    protected Logger $logger;

    /**
     * Connection instance
     *
     * @var Connection
     */
    protected Connection $connection;

    /**
     * Transaction constructor
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->logger = Logger::getInstance();
        $this->connection = $connection;
    }

    /**
     * Begin a new transaction
     *
     * @return self
     * @throws QueryException|Exception
     */
    public function begin(): self
    {
        $this->connection->getPdo()->beginTransaction();
        $this->logger->debug("Transaction started on '{$this->connection->getName()}'");
        return $this;
    }

    /**
     * Commit the transaction
     *
     * @return self
     * @throws QueryException
     * @throws Exception
     */
    public function commit(): self
    {
        $this->connection->getPdo()->commit();
        $this->logger->debug("Transaction committed on '{$this->connection->getName()}'");
        return $this;
    }

    /**
     * Roll back the transaction
     *
     * @return self
     * @throws Exception
     */
    public function rollback(): self
    {
        $pdo = $this->connection->getPdo();
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
            $this->logger->debug("Transaction rolled back on '{$this->connection->getName()}'");
        }
        return $this;
    }
}
