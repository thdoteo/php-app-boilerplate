<?php

namespace Tests\Framework\Twig;

use Framework\Twig\FormExtension;
use PHPUnit\Framework\TestCase;

class FormExtensionTest extends TestCase
{
    /**
     * @var FormExtension
     */
    private $formExtension;

    public function setUp()
    {
        $this->formExtension = new FormExtension();
    }

    private function trim(string $value)
    {
        $lines = explode(PHP_EOL, $value);
        $lines = array_map('trim', $lines);
        return implode('', $lines);
    }

    public function assertSimilar(string $expected, string $actual)
    {
        $this->assertEquals($this->trim($expected), $this->trim($actual));
    }

    public function testInput()
    {
        $html = $this->formExtension->field([], 'name', 'demo', 'title');
        $this->assertSimilar("
        <div class=\"form-group\">
            <label for=\"name\">title</label>
            <input class=\"form-control\" name=\"name\" id=\"name\" type=\"text\" value=\"demo\">
        </div>
        ", $html);
    }

    public function testFieldWithClass()
    {
        $html = $this->formExtension->field([], 'name', 'demo', 'title', ['class' => 'datepicker']);
        $this->assertSimilar("
        <div class=\"form-group\">
            <label for=\"name\">title</label>
            <input class=\"form-control datepicker\" name=\"name\" id=\"name\" type=\"text\" value=\"demo\">
        </div>
        ", $html);
    }

    public function testFieldWithErrors()
    {
        $context = ['errors' => ['name' => 'error text']];
        $html = $this->formExtension->field($context, 'name', 'demo', 'title');
        $this->assertSimilar("
        <div class=\"form-group\">
            <label for=\"name\">title</label>
            <input class=\"form-control is-invalid\" name=\"name\" id=\"name\" type=\"text\" value=\"demo\">
            <div class=\"form-text invalid-feedback\">error text</div>
        </div>
        ", $html);
    }

    public function testTextarea()
    {
        $html = $this->formExtension->field([], 'name', 'demo', 'title', ['type' => 'textarea']);
        $this->assertSimilar("
        <div class=\"form-group\">
            <label for=\"name\">title</label>
            <textarea class=\"form-control\" name=\"name\" id=\"name\">demo</textarea>
        </div>
        ", $html);
    }

    public function testSelect()
    {
        $html = $this->formExtension->field(
            [],
            'name',
            2,
            'title',
            ['options' => ['1' => 'demo1', '2' => 'demo2']]
        );
        $this->assertSimilar('
        <div class="form-group">
            <label for="name">title</label>
            <select class="form-control" name="name" id="name">
                <option value="1">demo1</option>
                <option value="2" selected>demo2</option>
            </select>
        </div>', $html);
    }
}