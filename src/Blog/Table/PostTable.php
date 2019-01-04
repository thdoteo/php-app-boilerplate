<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use Framework\Database\PaginatedQuery;
use Framework\Database\Query;
use Framework\Database\Table;
use Pagerfanta\Pagerfanta;

class PostTable extends Table
{
    protected $entity = Post::class;

    protected $table = 'posts';

    public function findAll(): Query
    {
        $categoryTable = new CategoryTable($this->getPdo());
        return $this->makeQuery()
            ->join($categoryTable->getTable() . ' as c', 'c.id = p.category_id')
            ->select('p.*, c.name as category_name, c.slug as category_slug')
            ->order('p.created_at DESC');
    }

    /**
     * Returns a Query to retrieve all public posts.
     *
     * @return Query
     */
    public function findPublic(): Query
    {
        return $this->findAll()
            ->where('p.published = 1')
            ->where('p.created_at < NOW()');
    }

    /**
     * Returns a Query to retrieve all public posts of a specific category.
     *
     * @param int $id
     * @return Query
     */
    public function findPublicOfCategory(int $id): Query
    {
        return $this->findPublic()->where("p.category_id = $id");
    }

    /**
     * Returns a Post with its category's information.
     *
     * @param int $id
     * @return Post
     */
    public function findWithCategory(int $id): Post
    {
        return $this->findPublic()->where("p.id = $id")->fetch();
    }
}
