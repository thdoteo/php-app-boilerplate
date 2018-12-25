<?php

use App\Blog\BlogModule;

return [
    'blog.prefix' => '/blog',
    BlogModule::class => \DI\autowire()->constructorParameter('prefix', \DI\get('blog.prefix'))
];
