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
 * QueryBuilder component for the Catalyst Framework
 *
 */

namespace Catalyst\Framework\Core\Database;

use Catalyst\Framework\Core\Exceptions\QueryException;
use Exception;

/**************************************************************************************
 * SQL Query Builder
 *
 * Provides a fluent interface for building SQL queries.
 *
 * @package Catalyst\Framework\Core\Database
 */
class QueryBuilder
{
    /**
     * The database connection
     *
     * @var Connection
     */
    protected Connection $connection;

    /**
     * The table the query is targeting
     *
     * @var string
     */
    protected string $table;

    /**
     * The columns to select
     *
     * @var array
     */
    protected array $columns = ['*'];

    /**
     * The where constraints
     *
     * @var array
     */
    protected array $wheres = [];

    /**
     * The order by clauses
     *
     * @var array
     */
    protected array $orders = [];

    /**
     * The group by clauses
     *
     * @var array
     */
    protected array $groups = [];

    /**
     * The having constraints
     *
     * @var array
     */
    protected array $havings = [];

    /**
     * The joins
     *
     * @var array
     */
    protected array $joins = [];

    /**
     * The maximum number of records to return
     *
     * @var int|null
     */
    protected ?int $limit = null;

    /**
     * The number of records to skip
     *
     * @var int|null
     */
    protected ?int $offset = null;

    /**
     * QueryBuilder constructor
     *
     * @param Connection $connection
     * @param string $table
     */
    public function __construct(Connection $connection, string $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    /**
     * Set the columns to be selected
     *
     * @param array|string $columns
     * @return self
     */
    public function select(array|string $columns = ['*']): self
    {
        $this->columns = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    /**
     * Add a where clause
     *
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @param string $boolean
     * @return self
     */
    public function where(string $column, string $operator, mixed $value, string $boolean = 'AND'): self
    {
        $this->wheres[] = [
            'type' => 'basic',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => $boolean
        ];

        return $this;
    }

    /**
     * Add an OR where clause
     *
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @return self
     */
    public function orWhere(string $column, string $operator, mixed $value): self
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    /**
     * Add a where equals clause
     *
     * @param string $column
     * @param mixed $value
     * @return self
     */
    public function whereEqual(string $column, mixed $value): self
    {
        return $this->where($column, '=', $value);
    }

    /**
     * Add a where in clause
     *
     * @param string $column
     * @param array $values
     * @param string $boolean
     * @return self
     */
    public function whereIn(string $column, array $values, string $boolean = 'AND'): self
    {
        $this->wheres[] = [
            'type' => 'in',
            'column' => $column,
            'values' => $values,
            'boolean' => $boolean
        ];

        return $this;
    }

    /**
     * Add an OR where in clause
     *
     * @param string $column
     * @param array $values
     * @return self
     */
    public function orWhereIn(string $column, array $values): self
    {
        return $this->whereIn($column, $values, 'OR');
    }

    /**
     * Add a where null clause
     *
     * @param string $column
     * @param string $boolean
     * @param bool $not
     * @return self
     */
    public function whereNull(string $column, string $boolean = 'AND', bool $not = false): self
    {
        $this->wheres[] = [
            'type' => 'null',
            'column' => $column,
            'boolean' => $boolean,
            'not' => $not
        ];

        return $this;
    }

    /**
     * Add a where not null clause
     *
     * @param string $column
     * @param string $boolean
     * @return self
     */
    public function whereNotNull(string $column, string $boolean = 'AND'): self
    {
        return $this->whereNull($column, $boolean, true);
    }

    /**
     * Add an order by clause
     *
     * @param string $column
     * @param string $direction
     * @return self
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orders[] = [
            'column' => $column,
            'direction' => $direction
        ];

        return $this;
    }

    /**
     * Add a group by clause
     *
     * @param array|string $columns
     * @return self
     */
    public function groupBy(array|string $columns): self
    {
        $this->groups = array_merge(
            $this->groups,
            is_array($columns) ? $columns : [$columns]
        );

        return $this;
    }

    /**
     * Add a having clause
     *
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @param string $boolean
     * @return self
     */
    public function having(string $column, string $operator, mixed $value, string $boolean = 'AND'): self
    {
        $this->havings[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => $boolean
        ];

        return $this;
    }

    /**
     * Add a join clause
     *
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @param string $type
     * @return self
     */
    public function join(
        string $table,
        string $first,
        string $operator,
        string $second,
        string $type = 'INNER'
    ): self
    {
        $this->joins[] = [
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second,
            'type' => $type
        ];

        return $this;
    }

    /**
     * Add a left join clause
     *
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @return self
     */
    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    /**
     * Add a right join clause
     *
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @return self
     */
    public function rightJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }

    /**
     * Set the limit
     *
     * @param int $limit
     * @return self
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Set the offset
     *
     * @param int $offset
     * @return self
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Set the limit and offset for pagination
     *
     * @param int $page
     * @param int $perPage
     * @return self
     */
    public function forPage(int $page, int $perPage): self
    {
        return $this->offset(($page - 1) * $perPage)->limit($perPage);
    }

    /**
     * Execute the query and get the first result
     *
     * @param array $columns
     * @return array|null
     * @throws QueryException|Exception
     */
    public function first(array $columns = ['*']): ?array
    {
        if (!empty($columns) && $columns !== ['*']) {
            $this->columns = $columns;
        }

        return $this->limit(1)->get()[0] ?? null;
    }

    /**
     * Execute the query and get all results
     *
     * @param array $columns
     * @return array
     * @throws QueryException
     * @throws Exception
     */
    public function get(array $columns = ['*']): array
    {
        if (!empty($columns) && $columns !== ['*']) {
            $this->columns = $columns;
        }

        [$query, $bindings] = $this->compileSelect();
        return $this->connection->select($query, $bindings);
    }

    /**
     * Execute the query as a "select" statement
     *
     * @param array $columns
     * @return array
     * @throws QueryException|Exception
     */
    public function all(array $columns = ['*']): array
    {
        return $this->get($columns);
    }

    /**
     * Count records
     *
     * @return int
     * @throws QueryException|Exception
     */
    public function count(): int
    {
        $result = $this->aggregate('COUNT');
        return (int)($result['aggregate'] ?? 0);
    }

    /**
     * Apply an aggregate function
     *
     * @param string $function
     * @param string $column
     * @return array|null
     * @throws QueryException|Exception
     */
    protected function aggregate(string $function, string $column = '*'): ?array
    {
        $this->columns = ["$function($column) as aggregate"];

        $results = $this->get();

        return $results[0] ?? null;
    }

    /**
     * Insert a new record
     *
     * @param array $values
     * @return int Last insert ID
     * @throws QueryException|Exception
     */
    public function insert(array $values): int
    {
        return $this->connection->insert($this->table, $values);
    }

    /**
     * Update records
     *
     * @param array $values
     * @return int Number of affected rows
     * @throws QueryException|Exception
     */
    public function update(array $values): int
    {
        [$query, $bindings] = $this->compileUpdate($values);
        return $this->connection->execute($query, $bindings);
    }

    /**
     * Delete records
     *
     * @return int Number of affected rows
     * @throws QueryException|Exception
     */
    public function delete(): int
    {
        [$query, $bindings] = $this->compileDelete();
        return $this->connection->execute($query, $bindings);
    }

    /**
     * Compile the select query
     *
     * @return array Query and bindings
     */
    protected function compileSelect(): array
    {
        $query = ['SELECT ' . $this->compileColumns()];
        $query[] = 'FROM ' . $this->table;

        $bindings = [];

        // Joins
        if (!empty($this->joins)) {
            foreach ($this->joins as $join) {
                $query[] = "{$join['type']} JOIN {$join['table']} ON {$join['first']} {$join['operator']} {$join['second']}";
            }
        }

        // Where clauses
        if (!empty($this->wheres)) {
            [$whereString, $whereBindings] = $this->compileWheres();
            $query[] = 'WHERE ' . $whereString;
            $bindings = array_merge($bindings, $whereBindings);
        }

        // Group by
        if (!empty($this->groups)) {
            $query[] = 'GROUP BY ' . implode(', ', $this->groups);
        }

        // Having
        if (!empty($this->havings)) {
            [$havingString, $havingBindings] = $this->compileHavings();
            $query[] = 'HAVING ' . $havingString;
            $bindings = array_merge($bindings, $havingBindings);
        }

        // Order by
        if (!empty($this->orders)) {
            $query[] = 'ORDER BY ' . $this->compileOrders();
        }

        // Limit and offset
        if ($this->limit !== null) {
            $query[] = "LIMIT $this->limit";
        }

        if ($this->offset !== null) {
            $query[] = "OFFSET $this->offset";
        }

        return [implode(' ', $query), $bindings];
    }

    /**
     * Compile columns for select
     *
     * @return string
     */
    protected function compileColumns(): string
    {
        return implode(', ', $this->columns);
    }

    /**
     * Compile the where clauses
     *
     * @return array Where string and bindings
     */
    protected function compileWheres(): array
    {
        $wheres = [];
        $bindings = [];

        foreach ($this->wheres as $i => $where) {
            $prefix = $i === 0 ? '' : $where['boolean'] . ' ';

            if ($where['type'] === 'basic') {
                $placeholder = "param_" . count($bindings);
                $wheres[] = $prefix . "{$where['column']} {$where['operator']} :$placeholder";
                $bindings[$placeholder] = $where['value'];
            } elseif ($where['type'] === 'in') {
                $placeholders = [];
                foreach ($where['values'] as $j => $value) {
                    $placeholder = "param_" . count($bindings);
                    $placeholders[] = ":$placeholder";
                    $bindings[$placeholder] = $value;
                }

                $wheres[] = $prefix . "{$where['column']} IN (" . implode(', ', $placeholders) . ")";
            } elseif ($where['type'] === 'null') {
                $wheres[] = $prefix . "{$where['column']} " . ($where['not'] ? 'IS NOT NULL' : 'IS NULL');
            }
        }

        return [implode(' ', $wheres), $bindings];
    }

    /**
     * Compile the order clauses
     *
     * @return string
     */
    protected function compileOrders(): string
    {
        $orders = [];

        foreach ($this->orders as $order) {
            $orders[] = "{$order['column']} {$order['direction']}";
        }

        return implode(', ', $orders);
    }

    /**
     * Compile the having clauses
     *
     * @return array Having string and bindings
     */
    protected function compileHavings(): array
    {
        $havings = [];
        $bindings = [];

        foreach ($this->havings as $i => $having) {
            $prefix = $i === 0 ? '' : $having['boolean'] . ' ';
            $placeholder = "having_" . count($bindings);

            $havings[] = $prefix . "{$having['column']} {$having['operator']} :$placeholder";
            $bindings[$placeholder] = $having['value'];
        }

        return [implode(' ', $havings), $bindings];
    }

    /**
     * Compile the update query
     *
     * @param array $values
     * @return array Query and bindings
     */
    protected function compileUpdate(array $values): array
    {
        $query = ["UPDATE $this->table SET"];
        $sets = [];
        $bindings = [];

        foreach ($values as $column => $value) {
            $placeholder = "set_" . count($bindings);
            $sets[] = "$column = :$placeholder";
            $bindings[$placeholder] = $value;
        }

        $query[] = implode(', ', $sets);

        if (!empty($this->wheres)) {
            [$whereString, $whereBindings] = $this->compileWheres();
            $query[] = 'WHERE ' . $whereString;
            $bindings = array_merge($bindings, $whereBindings);
        }

        return [implode(' ', $query), $bindings];
    }

    /**
     * Compile the delete query
     *
     * @return array Query and bindings
     */
    protected function compileDelete(): array
    {
        $query = ["DELETE FROM $this->table"];
        $bindings = [];

        if (!empty($this->wheres)) {
            [$whereString, $whereBindings] = $this->compileWheres();
            $query[] = 'WHERE ' . $whereString;
            $bindings = array_merge($bindings, $whereBindings);
        }

        return [implode(' ', $query), $bindings];
    }

    /**
     * Get the underlying database connection
     *
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }
}
