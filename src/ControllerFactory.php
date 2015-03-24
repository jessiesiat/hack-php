<?php

namespace Hack;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class ControllerFactory {
	
	protected $routes;
	public $methods = array();

	public function __construct(RouteCollection $routes)
	{
		$this->routes = $routes;
	}

	public function match($method, $pattern, $to = null)
	{
		$defaults = array(
			'_controller' => $to
		);
		$route = new Route($pattern, $defaults);
		$route->setMethods(explode('|', $method));
		$routeName = $this->generateRouteName($route);

		$this->routes->add($routeName, $route);
	}

	public function generateRouteName($route)
    {
        $requirements = $route->getRequirements();
        $method = isset($requirements['_method']) ? $requirements['_method'] : '';

        $routeName = $method.$route->getPath();
        $routeName = str_replace(array('/', ':', '|', '-'), '_', $routeName);
        $routeName = preg_replace('/[^a-z0-9A-Z_.]+/', '', $routeName);

        return $routeName;
    }

	public function allRoutes()
	{
		return $this->routes;
	}

}