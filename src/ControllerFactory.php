<?php

namespace Hack;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class ControllerFactory {
	
	protected $routes;

	public function __construct(RouteCollection $routes)
	{
		$this->routes = $routes;
	}

	public function route($method, $pattern, $to = null)
	{
		$routeName = $this->getRouteName($to);

		$this->routes->add($routeName, new Route($pattern, array(
			'_controller' => $to
		)));
	}

	public function getRouteName($to)
	{
		return uniqid('route');
	}

	public function allRoutes()
	{
		return $this->routes;
	}

}