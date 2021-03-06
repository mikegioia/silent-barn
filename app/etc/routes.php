<?php

$router = new Phalcon\Mvc\Router( FALSE );

// automatically remove trailing slashes
$router->removeExtraSlashes( TRUE );

// base application
$router->add(
    '/:controller/:action/:params', [
        'namespace' => 'Controllers',
        'controller' => 1,
        'action' => 2,
        'params' => 3
    ]);
$router->add(
    '/:controller[/]{0,1}', [
        'namespace' => 'Controllers',
        'controller' => 1
    ]);

// load the namespaces, array takes the form $segment => $namespace
foreach ( $config->app->modules as $segment => $namespace )
{
    $router->add(
        "/{$segment}/:controller/:action/:params", [
            'namespace' => 'Controllers\\'. $namespace,
            'controller' => 1,
            'action' => 2,
            'params' => 3,
        ]);
    $router->add(
        "/{$segment}/:controller[/]{0,1}", [
            'namespace' => 'Controllers\\'. $namespace,
            'controller' => 1,
        ]);
    $router->add(
        "/{$segment}[/]{0,1}", [
            'namespace' => 'Controllers\\'. $namespace,
            'controller' => 'index',
            'action' => 'index'
        ]);
}

// posts routes
$router->add(
    "/([0-9]{4})/([0-9]{2})/([a-zA-Z0-9_-]+)", [
        'namespace' => 'Controllers',
        'controller' => 'posts',
        'action' => 'show',
        'year' => 1,
        'month' => 2,
        'slug' => 3
    ]);

// donate route
$router->add(
    "/donate", [
        'namespace' => 'Controllers',
        'controller' => 'about',
        'action' => 'donate'
    ]);

// memberships
$router->add(
    "/memberships", [
        'namespace' => 'Controllers',
        'controller' => 'about',
        'action' => 'memberships'
    ]);
$router->add(
    "/membership", [
        'namespace' => 'Controllers',
        'controller' => 'about',
        'action' => 'memberships'
    ]);

return $router;