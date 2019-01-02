<?php

namespace Tests\Framework\Database;

use Framework\Database\Query;
use PHPUnit\Framework\TestCase;
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
}