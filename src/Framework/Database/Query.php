<?php

namespace Framework\Database;

use Pagerfanta\Pagerfanta;
use PDO;
use Traversable;

/**
 * Class Query
 * @package Framework\Database
 */
class Query implements \IteratorAggregate
{

    private $select;
    private $from;
    private $where = [];
    private $order = [];
    private $limit;
    private $joins;

    private $params = [];

    private $entity;

    /**
     * @var PDO|null
     */
    private $pdo;

    /**
     * Query constructor.
     * @param PDO|null $pdo
     */
    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * BUILD THE QUERY.
     */

    /**
     * Registers the FROM part of a MySQL query.
     *
     * @param string $table
     * @param string|null $alias
     * @return Query
     */
    public function from(string $table, ?string $alias = null): self
    {
        if ($alias) {
            $this->from[$table] = $alias;
        } else {
            $this->from[] = $table;
        }
        return $this;
    }

    /**
     * Registers the SELECT part of a MySQL query.
     *
     * @param string ...$fields
     * @return Query
     */
    public function select(string ...$fields): self
    {
        $this->select = $fields;
        return $this;
    }

    /**
     * Registers the WHERE part of a MySQL query.
     *
     * @param string ...$condition
     * @return Query
     */
    public function where(string ...$condition): self
    {
        $this->where = array_merge($this->where, $condition);
        return $this;
    }

    /**
     * Registers the LIMIT part of a MySQL query.
     *
     * @param int $length
     * @param int $offset
     * @return Query
     */
    public function limit(int $length, int $offset = 0): self
    {
        $this->limit = "$offset, $length";
        return $this;
    }

    /**
     * Registers the ORDER part of a MySQL query.
     *
     * @param string $order
     * @return Query
     */
    public function order(string $order): self
    {
        $this->order[] = $order;
        return $this;
    }

    /**
     * Registers a LEFT or INNER JOIN of a MySQL query.
     *
     * @param string $table
     * @param string $condition
     * @return Query
     */
    public function join(string $table, string $condition, string $type = 'left'): self
    {
        $this->joins[$type][] = [$table, $condition];
        return $this;
    }

    /**
     * Adds parameters to the current query.
     *
     * @param array $params
     * @return Query
     */
    public function params(array $params): self
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * Retrieves information as an object of a specific class.
     *
     * @param string $entity
     * @return Query
     */
    public function as(string $entity): self
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * USE THE QUERY.
     */

    /**
     * Gets the number of results.
     *
     * @return int
     */
    public function count(): int
    {
        $query = clone $this;
        $table = current($this->from);
        return $query->select("COUNT($table.id)")->execute()->fetchColumn();
    }

    /**
     * Gets all results.
     *
     * @return QueryResult
     */
    public function fetchAll(): QueryResult
    {
        return new QueryResult(
            $this->execute()->fetchAll(PDO::FETCH_ASSOC),
            $this->entity
        );
    }

    /**
     * Gets a result.
     *
     * @return bool|mixed
     */
    public function fetch()
    {
        $result = $this->execute()->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            return false;
        }
        if ($this->entity) {
            return Hydrator::hydrate($result, $this->entity);
        }
        return $result;
    }

    /**
     * Gets a result or fail.
     *
     * @return bool|mixed
     * @throws NoElementFoundException
     */
    public function fetchOrFail()
    {
        $result = $this->fetch();
        if ($result === false) {
            throw new NoElementFoundException();
        }
        return $result;
    }

    /**
     * Paginates results.
     *
     * @param int $perPage
     * @param int $currentPage
     * @return Pagerfanta
     */
    public function paginate(int $perPage, int $currentPage = 1): Pagerfanta
    {
        $paginator = new PaginatedQuery($this);
        return (new Pagerfanta($paginator))->setMaxPerPage($perPage)->setCurrentPage($currentPage);
    }

    /**
     * Executes the current query.
     *
     * @return bool|false|\PDOStatement
     */
    private function execute()
    {
        $query = $this->__toString();
        if (!empty($this->params)) {
            $statement = $this->pdo->prepare($query);
            $statement->execute($this->params);
            return $statement;
        } else {
            return $this->pdo->query($query);
        }
    }

    /**
     * Converts the current query to string.
     *
     * @return string
     */
    public function __toString()
    {
        // SELECT
        $parts = ['SELECT'];
        if ($this->select) {
            $parts[] = join(', ', $this->select);
        } else {
            $parts[] = '*';
        }

        // FROM
        $parts[] = 'FROM';
        $from = [];
        foreach ($this->from as $key => $value) {
            if (is_string($key)) {
                $from[] = "$key as $value";
            } else {
                $from[] = $value;
            }
        }
        $parts[] = join(', ', $from);

        // JOINS
        if (!empty($this->joins)) {
            foreach ($this->joins as $type => $joins) {
                foreach ($joins as [$table, $condition]) {
                    $parts[] = strtoupper($type) . " JOIN $table ON $condition";
                }
            }
        }

        // WHERE
        if (!empty($this->where)) {
            $parts[] = 'WHERE';
            $parts[] = '((' . join(') AND (', $this->where) . '))';
        }

        // ORDER
        if (!empty($this->order)) {
            $parts[] = 'ORDER BY';
            $parts[] = join(', ', $this->order);
        }

        // LIMIT
        if ($this->limit) {
            $parts[] = 'LIMIT ' . $this->limit;
        }

        return join(' ', $parts);
    }

    /**
     * Retrieve an external iterator.
     */
    public function getIterator()
    {
        return $this->fetchAll();
    }
}
