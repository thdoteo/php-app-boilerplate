<?php

require '../vendor/autoload.php';

// Choose modules to load
$modules = [
    \App\Blog\BlogModule::class
];

// Init Container
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . '/config/config.php');
//$builder->addDefinitions(dirname(__DIR__) . '/config.php');
foreach ($modules as $module) {
    if ($module::DEFINITIONS) {
        $builder->addDefinitions($module::DEFINITIONS);
    }
}
$container = $builder->build();

// Start App and handle requests
$app = new \Framework\App($container, $modules);
$response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());
\Http\Response\send($response);
