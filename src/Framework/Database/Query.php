<?php

namespace Framework\Database;

use PDO;

/**
 * Class Query
 * @package Framework\Database
 *
 * To do:
 * Groupby/order/limit
 * Paginate
 * Make query
 */
class Query
{

    private $select;

    private $from;

    private $where = [];

    private $group;

    private $order;

    private $limit;

    private $params;

    /**
     * @var PDO|null
     */
    private $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    public function from(string $table, ?string $alias = null): self
    {
        if ($alias) {
            $this->from[$alias] = $table;
        } else {
            $this->from[] = $table;
        }
        return $this;
    }

    public function select(string ...$fields): self
    {
        $this->select = $fields;
        return $this;
    }

    public function where(string ...$condition): self
    {
        $this->where = array_merge($this->where, $condition);
        return $this;
    }

    public function count(): int
    {
        $this->select('COUNT(id)');
        return $this->execute()->fetchColumn();
    }

    public function params(array $params): self
    {
        $this->params = $params;
        return $this;
    }

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
                $from[] = "$value as $key";
            } else {
                $from[] = $value;
            }
        }
        $parts[] = join(', ', $from);

        // WHERE
        if (!empty($this->where)) {
            $parts[] = 'WHERE';
            $parts[] = '((' . join(') AND (', $this->where) . '))';
        }

        return join(' ', $parts);
    }

    private function execute()
    {
        $query = $this->__toString();
        if ($this->params) {
            $statement = $this->pdo->prepare($query);
            $statement->execute($this->params);
            return $statement;
        } else {
            return $this->pdo->query($query);
        }
    }
}
