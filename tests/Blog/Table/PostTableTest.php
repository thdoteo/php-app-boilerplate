<?php

namespace Tests\App\Blog\Table;

use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use Framework\Database\NoElementFoundException;
use Tests\DatabaseTestCase;

class PostTableTest extends DatabaseTestCase
{
    /**
     * @var PostTable
     */
    private $postTable;

    public function setUp()
    {
        parent::setUp();
        $pdo = $this->getPdo();
        $this->migrate($pdo);
        $this->postTable = new PostTable($pdo);
    }

    public function testFind()
    {
        $this->seed($this->postTable->getPdo());
        $post = $this->postTable->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testFindNotFound()
    {
        $this->expectException(NoElementFoundException::class);
        $this->postTable->find(1);
    }

    public function testUpdate()
    {
        $this->seed($this->postTable->getPdo());

        $this->postTable->update(1, ['name' => 'Hello', 'slug' => 'demo']);
        $post = $this->postTable->find(1);
        $this->assertEquals('Hello', $post->name);
        $this->assertEquals('demo', $post->slug);
    }

    public function testInsert()
    {
        $this->postTable->insert(['name' => 'Hello', 'slug' => 'demo']);
        $post = $this->postTable->find(1);
        $this->assertEquals('Hello', $post->name);
        $this->assertEquals('demo', $post->slug);
    }

    public function testDelete()
    {
        $this->postTable->insert(['name' => 'Hello', 'slug' => 'demo']);
        $this->postTable->insert(['name' => 'Hello', 'slug' => 'demo']);

        $count = $this->postTable->getPdo()->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(2, (int) $count);

        $this->postTable->delete($this->postTable->getPdo()->lastInsertId());

        $count = $this->postTable->getPdo()->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(1, (int) $count);
    }
}
