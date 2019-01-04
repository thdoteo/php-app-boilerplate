<?php

namespace Tests\Framework\Database;

use Framework\Database\Query;
use Tests\DatabaseTestCase;

class QueryTest extends DatabaseTestCase
{
    public function testSimpleQuery()
    {
        $query = (new Query())
            ->from('posts')
            ->select('name');

        $this->assertEquals('SELECT name FROM posts', (string)$query);
    }

    public function testQueryWithWhere()
    {
        $query = (new Query())
            ->from('posts', 'p')
            ->where('a = :a OR b = :b', 'c = :c');

        $query2 = (new Query())
            ->from('posts', 'p')
            ->where('a = :a OR b = :b')
            ->where('c = :c');

        $this->assertEquals('SELECT * FROM posts as p WHERE ((a = :a OR b = :b) AND (c = :c))', (string)$query);
        $this->assertEquals('SELECT * FROM posts as p WHERE ((a = :a OR b = :b) AND (c = :c))', (string)$query2);
    }

    public function testFetchAll()
    {
        $pdo = $this->getPdo();
        $this->migrate($pdo);
        $this->seed($pdo);

        $posts = (new Query($pdo))
            ->from('posts', 'p')
            ->count();
        $this->assertEquals(100, $posts);

        $posts = (new Query($pdo))
            ->from('posts', 'p')
            ->where('p.id < :number')
            ->params([
                'number' => 30
            ])
            ->count();
        $this->assertEquals(29, $posts);
    }

    public function testEntity()
    {
        $pdo = $this->getPdo();
        $this->migrate($pdo);
        $this->seed($pdo);

        $posts = (new Query($pdo))
            ->from('posts', 'p')
            ->as(Demo::class)
            ->fetchAll();
        $this->assertEquals('demo', substr($posts[0]->getSlug(), -4));
    }

    public function testLazyHydrateEntity()
    {
        $pdo = $this->getPdo();
        $this->migrate($pdo);
        $this->seed($pdo);

        $posts = (new Query($pdo))
            ->from('posts', 'p')
            ->as(Demo::class)
            ->fetchAll();

        $post = $posts[0];
        $post2 = $posts[0];
        $this->assertSame($post, $post2);
    }

    public function testLimitAndOrderQuery()
    {
        $query = (new Query())
            ->from('posts', 'p')
            ->select('name')
            ->order('id DESC')
            ->order('name ASC')
            ->limit(10, 5);

        $this->assertEquals('SELECT name FROM posts as p ORDER BY id DESC, name ASC LIMIT 5, 10', (string)$query);
    }

    public function testJoinQuery()
    {
        $query = (new Query())
            ->from('posts', 'p')
            ->select('name')
            ->join('categories as c', 'c.id = p.category_id')
            ->join('categories as c2', 'c2.id = p.category_id', 'inner');

        $this->assertEquals('SELECT name FROM posts as p LEFT JOIN categories as c ON c.id = p.category_id INNER JOIN categories as c2 ON c2.id = p.category_id', (string)$query);
    }
}