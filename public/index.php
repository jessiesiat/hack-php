<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;

/**
 * -----------------------------------------------------------------
 * Initialize our application and send the response
 * -----------------------------------------------------------------
 */

$app = new Hack\Application();

$response = $app->handle($request = Request::createFromGlobals());

$response->send();

$app->terminate($request, $response);
