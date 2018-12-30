<?php

namespace Tests\Framework;

use Framework\Validator;
use PHPUnit\Framework\TestCase;
use Tests\DatabaseTestCase;

class ValidatorTest extends DatabaseTestCase
{

    public function testNotEmpty()
    {
        $errors = (new Validator([
            'name' => 'joe',
            'content' => ''
        ]))
            ->notEmpty('content')
            ->getErrors();
        $this->assertCount(1, $errors);
    }

    public function testRequiredIfFail()
    {
        $errors = (new Validator([
            'name' => 'joe'
        ]))
            ->required('name', 'content')
            ->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('The field content is required.', (string)$errors['content']);
    }

    public function testRequiredIfSuccess()
    {
        $errors = (new Validator([
            'name' => 'joe',
            'content' => 'test'
        ]))
            ->required('name', 'content')
            ->getErrors();
        $this->assertCount(0, $errors);
    }

    public function testSlugSuccess()
    {
        $errors = (new Validator([
            'slug1' => 'aa-eeee43',
            'slug2' => 'qwww'
        ]))
            ->slug('slug1')
            ->slug('slug2')
            ->getErrors();
        $this->assertCount(0, $errors);
    }

    public function testLength()
    {
        $params = ['slug' => '123456789'];

        $errors = (new Validator($params))->length('slug', 3)->getErrors();
        $this->assertCount(0, $errors);

        $errors = (new Validator($params))->length('slug', 12)->getErrors();
        $this->assertCount(1, $errors);

        $errors = (new Validator($params))->length('slug', 3, 4)->getErrors();
        $this->assertCount(1, $errors);

        $errors = (new Validator($params))->length('slug', 3, 20)->getErrors();
        $this->assertCount(0, $errors);

        $errors = (new Validator($params))->length('slug', null, 20)->getErrors();
        $this->assertCount(0, $errors);

        $errors = (new Validator($params))->length('slug', null, 8)->getErrors();
        $this->assertCount(1, $errors);
    }

    public function testSlugError()
    {
        $errors = (new Validator([
            'slug1' => 'aa-ee_ee43',
            'slug2' => 'aa--ee-ee43',
            'slug3' => 'aa-Ee-ee43',
            'slug4' => 'aa-ee-ee43-'
        ]))
            ->slug('slug1')
            ->slug('slug2')
            ->slug('slug3')
            ->slug('slug4')
            ->getErrors();
        $this->assertEquals(['slug1', 'slug2', 'slug3', 'slug4'], array_keys($errors));
    }

    public function testDatetime()
    {
        $errors = (new Validator([
            'date' => '2018-12-12 11:12:13'
        ]))
            ->datetime('date')
            ->getErrors();
        $this->assertCount(0, $errors);

        $errors = (new Validator([
            'date' => '2018-12-12'
        ]))
            ->datetime('date')
            ->getErrors();
        $this->assertCount(1, $errors);

        $errors = (new Validator([
            'date' => '2018-21-12 11:12:13'
        ]))
            ->datetime('date')
            ->getErrors();
        $this->assertCount(1, $errors);

        $errors = (new Validator([
            'date' => '2013-02-29 11:12:13'
        ]))
            ->datetime('date')
            ->getErrors();
        $this->assertCount(1, $errors);
    }

    public function testExists()
    {
        $pdo = $this->getPdo();
        $pdo->exec('CREATE TABLE test (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(255))');
        $pdo->exec('INSERT INTO test (name) VALUES ("a1")');
        $pdo->exec('INSERT INTO test (name) VALUES ("a2")');
        $this->assertTrue((new Validator([
            'category' => 1
        ]))->exists('category', 'test', $pdo)->isValid());
        $this->assertFalse((new Validator([
            'category' => 1222222
        ]))->exists('category', 'test', $pdo)->isValid());
    }

    public function testUnique()
    {
        $pdo = $this->getPdo();
        $pdo->exec('CREATE TABLE test (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(255))');
        $pdo->exec('INSERT INTO test (name) VALUES ("a1")');
        $pdo->exec('INSERT INTO test (name) VALUES ("a2")');

        $this->assertFalse((new Validator([
            'name' => 'a1'
        ]))->unique('name', 'test', $pdo)->isValid());

        $this->assertTrue((new Validator([
            'name' => 'a1111'
        ]))->unique('name', 'test', $pdo)->isValid());

        $this->assertTrue((new Validator([
            'name' => 'a1'
        ]))->unique('name', 'test', $pdo, 1)->isValid());

        $this->assertFalse((new Validator([
            'name' => 'a2'
        ]))->unique('name', 'test', $pdo)->isValid());
    }
}