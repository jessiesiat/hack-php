<?php

namespace Hack;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ControllerFactory {
    
    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    protected $routes;

    /** 
     * @var array  Controllers
     */
    protected $controllers = [];

    /**
     * Initialize the routes collection object
     *
     * @param \Symfony\Component\Routing\RouteCollection  $routes
     */
    public function __construct(RouteCollection $routes = null)
    {
        $this->routes = $routes ?: new RouteCollection;
    }

    /**
     * Adds a new route 
     *
     * @param  string  $method   Http method to use
     * @param  string  $pattern  Url path to match for this route
     * @param  mixed   $to       Controller to run when the path is match
     * @return void
     */
    public function match($method, $pattern, $to = null)
    {
        $defaults = array(
            '_controller' => $to
        );
        $route = new Route($pattern, $defaults);
        $route->setMethods(explode('|', $method));

        $this->controllers[] = $controller = new Controller($route);

        return $controller;
    }

    /**
     * Generates a route name
     * 
     * @param  \Symfony\Component\Routing\Route  $route
     * @return string
     */
    public function generateRouteName($route)
    {
        $requirements = $route->getRequirements();
        $method = isset($requirements['_method']) ? $requirements['_method'] : '';

        $routeName = $method.$route->getPath();
        $routeName = str_replace(array('/', ':', '|', '-'), '_', $routeName);
        $routeName = preg_replace('/[^a-z0-9A-Z_.]+/', '', $routeName);

        return $routeName;
    }
    
    /** 
     * Flush routes definition
     * 
     * @return  Symfony\Component\Routing\RouteCollection
     */
    public function flush()
    {
        $routes = $this->routes;

        foreach ($this->controllers as $controller) {
            if (!$name = $controller->getRouteName()) {
                $name = $controller->generateRouteName();
            }
            while($routes->get($name)) {
                $name .= '_';
            }
            $controller->bind($name);

            $routes->add($name, $controller->getRoute());
        }

        return $routes;
    }

}