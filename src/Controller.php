<?php

namespace Hack;

use Symfony\Component\Routing\Route;

class Controller 
{
	/**
	 * @var \Symfony\Component\Routing\Route
	 */
	protected $route;

	/**
	 * @var string
	 */
	protected $routeName;

	/** 
	 * Creates new Controller
	 * 
	 * @param $route  \Symfony\Component\Routing\Route
	 */
	public function __construct(Route $route)
	{
		$this->route = $route;
	}

	/**
	 * Get the Route instance
	 *
	 * @return \Symfony\Component\Routing\Route
	 */
	public function getRoute()
	{
		return $this->route;
	}

	/**
	 * Set's the Route name. Same as as 'method'
	 * 
	 * @param  string  Route name
	 * @return void
	 */
	public function bind($name)
	{
		$this->routeName = $name;
	}	

	/**
	 * Get the route name
	 * 
	 * @return string  Route name
	 */
	public function getRouteName()
	{
		return $this->routeName;
	}

	/**
	 * Generates a route name
	 * 
	 * @param  \Symfony\Component\Routing\Route  $route
	 * @return string
	 */
	public function generateRouteName()
    {
        $requirements = $this->route->getRequirements();
        $method = isset($requirements['_method']) ? $requirements['_method'] : '';

        $routeName = $method.$this->route->getPath();
        $routeName = str_replace(array('/', ':', '|', '-'), '_', $routeName);
        $routeName = preg_replace('/[^a-z0-9A-Z_.]+/', '', $routeName);

        return $routeName;
    }

}