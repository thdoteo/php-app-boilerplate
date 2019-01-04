<?php

namespace App\Blog\Actions;

use App\Blog\Table\CategoryTable;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface as Request;

class CategoryCrudAction extends CrudAction
{

    protected $routesPrefix = 'blog.admin.categories';

    protected $viewsPrefix = '@blog/admin/categories';

    /**
     * PostCrudAction constructor.
     * @param RendererInterface $renderer
     * @param Router $router
     * @param CategoryTable $table
     * @param FlashService $flashService
     */
    public function __construct(
        RendererInterface $renderer,
        Router $router,
        CategoryTable $table,
        FlashService $flashService
    ) {
        parent::__construct($renderer, $router, $table, $flashService);
    }

    protected function getParams(Request $request, $item): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug']);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function getValidator(Request $request)
    {
        return parent::getValidator($request)
            ->required('name', 'slug')
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->slug('slug')
            ->unique('slug', $this->table->getTable(), $this->table->getPdo(), $request->getAttribute('id'));
    }
}
