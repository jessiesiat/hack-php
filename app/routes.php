<?php 

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$routes = new RouteCollection();
$routes->add('foo', new Route('/foo', array(
	'_controller' => 'App\\Controllers\\FooController::show'
)));
$routes->add('hello', new Route('/hello/{name}', array('_controller' => 
	function(Request $request) {
		return new Response(sprintf("Hello %s", $request->get('name')));
	}
)));

return $routes;