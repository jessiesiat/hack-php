<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;

/**
 * -----------------------------------------------------------------
 * Initialize our application and send the response
 * -----------------------------------------------------------------
 */

$app = new Hack\Foundation\Application();

$app->get('/', 'App\\Controllers\\HelloController::index');
$app->get('/foo', 'App\\Controllers\\FooController::show');
$app->get('hello/{name}', function(Request $request) {
	return 'Hello '.$request->get('name');
});

$app->run();
