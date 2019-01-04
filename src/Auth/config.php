<?php

return [
    'auth.prefix' => '/auth',
    \Framework\Auth::class => DI\get(\App\Auth\DatabaseAuth::class),
    'twig.extensions' => \DI\add([
        DI\get(\App\Auth\Twig\AuthExtension::class)
    ])
];
