<?php

namespace Tests\Framework;

use Framework\Renderer;
use PHPUnit\Framework\TestCase;

class RendererTest extends TestCase
{

    private $renderer;

    public function setUp()
    {
        $this->renderer = new Renderer();
        $this->renderer->addPath(__DIR__ . '/views');
    }

    public function testRenderTheRightPath()
    {
        $this->renderer->addPath('blog', __DIR__ . '/views');
        $content = $this->renderer->render('@blog/demo');
        $this->assertEquals('hey guys', $content);
    }

    public function testRenderTheDefaultPath()
    {
        $content = $this->renderer->render('demo');
        $this->assertEquals('hey guys', $content);
    }

    public function testRenderWithParams()
    {
        $content = $this->renderer->render('demoWithParams', ['name' => 'theo']);
        $this->assertEquals('Hello theo', $content);
    }

    public function testRenderWithGlobalParams()
    {
        $this->renderer->addGlobal('name', 'Theo');
        $content = $this->renderer->render('demoWithParams', ['name' => 'theo']);
        $this->assertEquals('Hello theo', $content);
    }

}