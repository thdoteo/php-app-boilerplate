<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\PostUpload;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ResponseInterface;
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
     * @var PostUpload
     */
    private $postUpload;

    /**
     * PostCrudAction constructor.
     * @param RendererInterface $renderer
     * @param Router $router
     * @param PostTable $table
     * @param FlashService $flashService
     * @param CategoryTable $categoryTable
     * @param PostUpload $postUpload
     */
    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $table,
        FlashService $flashService,
        CategoryTable $categoryTable,
        PostUpload $postUpload
    ) {
        parent::__construct($renderer, $router, $table, $flashService);
        $this->categoryTable = $categoryTable;
        $this->postUpload = $postUpload;
    }

    /**
     * Deletes an element
     * @param Request $request
     * @return ResponseInterface
     * @throws \Framework\Database\NoElementFoundException
     */
    public function delete(Request $request)
    {
        $post = $this->table->find($request->getAttribute('id'));
        $this->postUpload->deleteFile($post->image);
        return parent::delete($request);
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

    /**
     * @param Request $request
     * @param Post $post
     * @return array
     */
    protected function getParams(Request $request, $post): array
    {
        $fields = ['name', 'content', 'slug', 'created_at', 'category_id'];
        $params = array_merge($request->getUploadedFiles(), $request->getParsedBody());

        if (!empty($params['image']->getClientFilename())) {
            $fields[] = 'image';
            $params['image'] = $this->postUpload->upload($params['image'], $post->image);
        }

        $params = array_filter($params, function ($key) use ($fields) {
            return in_array($key, $fields);
        }, ARRAY_FILTER_USE_KEY);
        $params = array_merge($params, [
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $params;
    }

    protected function getValidator(Request $request)
    {
        $validator = parent::getValidator($request)
            ->required('content', 'name', 'slug', 'created_at', 'category_id')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->datetime('created_at')
            ->slug('slug')
            ->exists('category_id', $this->categoryTable->getTable(), $this->categoryTable->getPdo())
            ->extension('image', ['png', 'jpg', 'jpeg']);
        if (is_null($request->getAttribute('id'))) {
            $validator->uploaded('image');
        }
        return $validator;
    }
}
