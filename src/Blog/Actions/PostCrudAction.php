<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostCrudAction extends CrudAction
{

    protected $routesPrefix = 'blog.admin';

    protected $viewsPrefix = '@blog/admin/posts';
    /**
     * @var CategoryTable
     */
    private $categoryTable;

    /**
     * PostCrudAction constructor.
     * @param RendererInterface $renderer
     * @param Router $router
     * @param PostTable $table
     * @param FlashService $flashService
     * @param CategoryTable $categoryTable
     */
    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $table,
        FlashService $flashService,
        CategoryTable $categoryTable
    ) {
        parent::__construct($renderer, $router, $table, $flashService);
        $this->categoryTable = $categoryTable;
    }

    /**
     * Adds categories data for the view
     * @param $params
     * @return array
     */
    protected function formParams(array $params): array
    {
        $params['categories'] = $this->categoryTable->findList();
        return $params;
    }

    protected function getNewEntity()
    {
        $post = new Post();
        $post->created_at = new \DateTime();
        return $post;
    }

    protected function getParams(Request $request): array
    {
        $params = array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'content', 'slug', 'created_at', 'category_id']);
        }, ARRAY_FILTER_USE_KEY);

        $params = array_merge($params, [
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $params;
    }

    protected function getValidator(Request $request)
    {
        return parent::getValidator($request)
            ->required('content', 'name', 'slug', 'created_at', 'category_id')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->datetime('created_at')
            ->slug('slug')
            ->exists('category_id', $this->categoryTable->getTable(), $this->categoryTable->getPdo());
    }
}
