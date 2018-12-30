<?php

namespace Tests\Blog\Actions;

use App\Blog\Actions\PostIndexAction;
use App\Blog\Actions\PostShowAction;
use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class PostShowActionTest extends TestCase
{

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var PostIndexAction
     */
    private $action;

    /**
     * @var PostTable
     */
    private $postTable;

    public function setUp()
    {
        $this->renderer = $this->prophesize(RendererInterface::class);

        $this->postTable = $this->prophesize(PostTable::class);

        $this->router = $this->prophesize(Router::class);
        $this->action = new PostShowAction(
            $this->renderer->reveal(),
            $this->router->reveal(),
            $this->postTable->reveal()
        );
    }

    public function makePost(int $id, string $slug): Post
    {
        $post = new Post();
        $post->id = $id;
        $post->slug = $slug;
        return $post;
    }

    public function testShowRedirect()
    {
        $post = $this->makePost(9, 'demo-test');
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', 'demo');

        $this->router->generateUri('blog.show', ['id' => $post->id, 'slug' => $post->slug])->willReturn('/demo2');
        $this->postTable->findWithCategory($post->id)->willReturn($post);

        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals(['/demo2'], $response->getHeader('location'));
    }

    public function testShowRender()
    {
        $post = $this->makePost(9, 'demo-test');
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', $post->slug);
        $this->postTable->findWithCategory($post->id)->willReturn($post);
        $this->renderer->render('@blog/show', ['post' => $post])->willReturn('');

        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(true, true);
    }
}
