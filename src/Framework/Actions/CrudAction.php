<?php

namespace Framework\Actions;

use App\Blog\Entity\Post;
use Framework\Database\Hydrator;
use Framework\Database\Table;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface;

class CrudAction
{
    /**
     * @var Table
     */
    protected $table;

    /**
     * @var string
     */
    protected $viewsPrefix;

    /**
     * @var string
     */
    protected $routesPrefix;

    /**
     * @var string[]
     */
    protected $messages = [
      'create' => 'The element has been successfully created.',
      'edit' => 'The element has been successfully modified.',
      'delete' => 'The element has been successfully removed.'
    ];

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var FlashService
     */
    private $flashService;

    use RouterAwareAction;

    /**
     * CrudAction constructor.
     * @param RendererInterface $renderer
     * @param Router $router
     * @param Table $table
     * @param FlashService $flashService
     */
    public function __construct(
        RendererInterface $renderer,
        Router $router,
        Table $table,
        FlashService $flashService
    ) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->table = $table;
        $this->flashService = $flashService;
    }

    /**
     * Handles request
     * @param Request $request
     * @return ResponseInterface|string
     * @throws \Exception
     */
    public function __invoke(Request $request)
    {
        $this->renderer->addGlobal('viewsPrefix', $this->viewsPrefix);
        $this->renderer->addGlobal('routesPrefix', $this->routesPrefix);

        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (substr($request->getUri(), -6) === 'create') {
            return $this->create($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }

        return $this->index($request);
    }

    /**
     * Lists elements
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $posts = $this->table->findAll()->paginate(12, $params['p'] ?? 1);
        return $this->renderer->render($this->viewsPrefix . '/index', [
            'items' => $posts
        ]);
    }

    /**
     * Creates an element
     * @param Request $request
     * @return ResponseInterface|string
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $item = $this->getNewEntity();

        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->insert($this->getParams($request, $item));
                $this->flashService->success($this->messages['create']);
                return $this->redirect($this->routesPrefix . '.index');
            }
            $errors = $validator->getErrors();
            Hydrator::hydrate($request->getParsedBody(), $item);
        }

        return $this->renderer->render(
            $this->viewsPrefix . '/create',
            $this->formParams(compact('item', 'errors'))
        );
    }

    /**
     * Edits an element
     * @param Request $request
     * @return ResponseInterface|string
     * @throws \Framework\Database\NoElementFoundException
     */
    public function edit(Request $request)
    {
        $item = $this->table->find($request->getAttribute('id'));

        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->update($item->id, $this->getParams($request, $item));
                $this->flashService->success($this->messages['edit']);
                return $this->redirect($this->routesPrefix . '.index');
            }
            $errors = $validator->getErrors();
            Hydrator::hydrate($request->getParsedBody(), $item);
        }

        return $this->renderer->render(
            $this->viewsPrefix . '/edit',
            $this->formParams(compact('item', 'errors'))
        );
    }

    /**
     * Deletes an element
     * @param Request $request
     * @return ResponseInterface
     */
    public function delete(Request $request)
    {
        $this->table->delete($request->getAttribute('id'));
        $this->flashService->success($this->messages['delete']);
        return $this->redirect($this->routesPrefix . '.index');
    }

    /**
     * Handles params before they are sent to the view
     * @param array $params
     * @return array
     */
    protected function formParams(array $params): array
    {
        return $params;
    }

    protected function getParams(Request $request, $item): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, []);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param Request $request
     * @return Validator
     */
    protected function getValidator(Request $request)
    {
        return new Validator(array_merge($request->getParsedBody(), $request->getUploadedFiles()));
    }

    /**
     * @return array
     */
    protected function getNewEntity()
    {
        return [];
    }
}
