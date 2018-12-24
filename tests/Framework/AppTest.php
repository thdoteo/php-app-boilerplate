<?php

namespace Tests\Framework;

use Framework\App;
use App\Blog;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{

    public function testRedirectTrailingSlash()
    {
        $app = new App();
        $request = new ServerRequest('GET', '/demoslash/');
        $response = $app->run($request);
        
        $this->assertContains('/demoslash', $response->getHeader('Location'));
        $this->assertEquals(301, $response->getStatusCode());
    }

    public function testBlog()
    {
        $app = new App([
            \App\Blog\BlogModule::class
        ]);

        $request = new ServerRequest('GET', '/blog');
        $requestSingle = new ServerRequest('GET', '/blog/test');

        $response = $app->run($request);
        $responseSingle = $app->run($requestSingle);

        $this->assertContains('welcome on the blog', (string)$response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('show test', (string)$responseSingle->getBody());
    }
}
