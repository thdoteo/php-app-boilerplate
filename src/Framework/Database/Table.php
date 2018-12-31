<?php

namespace Framework\Database;

use Pagerfanta\Pagerfanta;
use PDO;

class Table
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string|null
     */
    protected $entity;

    /**
     * Table constructor.
     * @param PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get all elements
     * @return array
     */
    public function findAll(): array
    {
        $query = $this->pdo->query("SELECT * FROM {$this->table}");
        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        } else {
            $query->setFetchMode(PDO::FETCH_OBJ);
        }
        return $query->fetchAll();
    }

    /**
     * Get an element by a property
     * @param string $field
     * @param string $value
     * @return array
     * @throws NoElementFoundException
     */
    public function findBy(string $field, string $value)
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE {$field} = ?", [$value]);
    }

    /**
     * Paginates a set of elements
     * @param int $perPage
     * @param int $currentPage
     * @return Pagerfanta
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            $this->paginationQuery(),
            "SELECT COUNT(id) FROM {$this->table}",
            $this->entity
        );
        return (new Pagerfanta($query))->setMaxPerPage($perPage)->setCurrentPage($currentPage);
    }

    protected function paginationQuery(): string
    {
        return "SELECT * FROM {$this->table}";
    }

    /**
     * Returns a list key=>value of elements
     * @return array
     */
    public function findList(): array
    {
        $results = $this->pdo->query("SELECT id, name FROM {$this->table}")
            ->fetchAll(PDO::FETCH_NUM);
        $list = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }
        return $list;
    }

    /**
     * Returns an element from its id
     * @param int $id
     * @return mixed
     * @throws NoElementFoundException
     */
    public function find(int $id)
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    /**
     * Returns the number of elements
     */
    public function count(): int
    {
        return $this->fetchColumn("SELECT COUNT(id) FROM {$this->table}");
    }

    /**
     * Inserts a new element
     * @param array $params
     * @return bool
     */
    public function insert(array $params): bool
    {
        $fields = array_keys($params);
        $values = join(',', array_map(function ($field) {
            return ':' . $field;
        }, $fields));
        $fields = join(',', $fields);

        $query = $this->pdo->prepare("INSERT INTO {$this->table} ({$fields}) VALUES ({$values})");
        return $query->execute($params);
    }

    /**
     * Updates the element with a specific id
     * @param int $id
     * @param array $params
     * @return bool
     */
    public function update(int $id, array $params): bool
    {
        $fieldQuery = $this->buildFieldQuery($params);
        $params["id"] = $id;
        $query = $this->pdo->prepare("UPDATE {$this->table} SET {$fieldQuery} WHERE id = :id");
        return $query->execute($params);
    }

    /**
     * Deletes an element
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $query = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $query->execute([$id]);
    }

    /**
     * Checks that an element with a specific id exists
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        $query = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE id = ?");
        $query->execute([$id]);
        return $query->fetchColumn() !== false;
    }

    private function buildFieldQuery(array $params)
    {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }

    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Returns the first element of a query
     * @param string $query
     * @param array $params
     * @return mixed
     * @throws NoElementFoundException
     */
    protected function fetchOrFail(string $query, array $params = [])
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        if ($this->entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        $result = $query->fetch();
        if ($result === false) {
            throw new NoElementFoundException();
        }
        return $result;
    }

    /**
     * Returns the first column
     * @param string $query
     * @param array $params
     * @return mixed
     */
    private function fetchColumn(string $query, array $params = [])
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        if ($this->entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        return $query->fetchColumn();
    }
}
