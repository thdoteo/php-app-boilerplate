<?php

namespace Framework\Renderer;

use Psr\Container\ContainerInterface;

class TwigRendererFactory
{
    public function __invoke(ContainerInterface $container): TwigRenderer
    {
        $path = $container->get('views.path');
        $loader = new \Twig_Loader_Filesystem($path);
        $twig = new \Twig_Environment($loader);
        if ($container->has('twig.extensions')) {
            foreach ($container->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);
            }
        }
        return new TwigRenderer($twig);
    }
}
