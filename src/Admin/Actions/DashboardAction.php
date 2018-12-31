<?php

namespace App\Admin\Actions;

use App\Admin\AdminWidgetInterface;
use Framework\Renderer\RendererInterface;

class DashboardAction
{

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var AdminWidgetInterface[]
     */
    private $widgets;

    /**
     * DashboardAction constructor.
     * @param RendererInterface $renderer
     * @param array $widgets
     */
    public function __construct(RendererInterface $renderer, array $widgets)
    {
        $this->renderer = $renderer;
        $this->widgets = $widgets;
    }

    public function __invoke()
    {
        $widgets = array_reduce($this->widgets, function (string $html, AdminWidgetInterface $widget) {
            return $html . $widget->render();
        }, '');
        return $this->renderer->render('@admin/dashboard', compact('widgets'));
    }
}
