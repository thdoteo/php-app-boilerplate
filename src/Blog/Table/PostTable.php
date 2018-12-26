<?php

namespace App\Blog\Table;

class PostTable
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * PostTable constructor.
     * @param PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return \stdClass[]
     */
    public function findPaginated(): array
    {
        return $this->pdo
            ->query('SELECT * FROM posts ORDER BY created_at DESC LIMIT 10')
            ->fetchAll();
    }

    /**
     * Get an article from its id
     * @param int $id
     * @return stdClass
     */
    public function find(int $id): \stdClass
    {
        $query = $this->pdo->prepare('SELECT * FROM posts WHERE id = ?');
        $query->execute([$id]);
        $post = $query->fetch();
        return $post;
    }
}