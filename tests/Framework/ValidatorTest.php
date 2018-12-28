<?php

namespace Tests\Framework;

use Framework\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
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
            'slug' => 'aa-eeee43'
        ]))
            ->slug('slug')
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
            'slug3' => 'aa-Ee-ee43'
        ]))
            ->slug('slug1')
            ->slug('slug2')
            ->slug('slug3')
            ->getErrors();
        $this->assertCount(3, $errors);
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
}