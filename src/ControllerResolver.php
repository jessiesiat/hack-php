<?php

namespace Hack;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

class ControllerResolver implements ControllerResolverInterface 
{
	/**
     * {@inheritdoc} 
     */
	public function getController(Request $request)
	{
		if (!$controller = $request->attributes->get('_controller')) {
			return false;
		}

		if ((is_array($controller) || is_object($controller) 
			|| function_exists($controller)) 
			&& is_callable($controller)) {
			return $controller;
		}

		$callable = $this->createController($controller);

		if (!is_callable($callable)) {
			throw new \InvalidArgumentException(sprintf('Controller "%s" for URI "%s" is not a callable.', $controller, $request->getPathInfo()));
		}

		return $callable;
	}

	/**
	 * Transform controller into a callable
	 * 
	 * @param  string  $controller
	 * @return callable
	 */
	protected function createController($controller)
	{
		if (false === strpos($controller, '@')
			&& false === strpos($controller, '::')) {
			throw new \InvalidArgumentException(sprintf('Unable to find controller "%s".', $controller));
		}

		$explode = strpos($controller, '::') ? '::' : '@';
		list($class, $method) = explode($explode, $controller, 2);

		if (!class_exists($class)) {
			throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
		}

		return array(new $class(), $method);
	}

    /**
     * {@inheritdoc} 
     */
    public function getArguments(Request $request, $controller)
    {
    	if (is_array($controller)) {
            $r = new \ReflectionMethod($controller[0], $controller[1]);
        } elseif (is_object($controller) && !$controller instanceof \Closure) {
            $r = new \ReflectionObject($controller);
            $r = $r->getMethod('__invoke');
        } else {
            $r = new \ReflectionFunction($controller);
        }

        return $this->doGetArguments($request, $controller, $r->getParameters());
    }

    /**
     * Match and assign controller parameters base on request attributes
     * 
     * @param  \Symfony\Component\HttpFoundation\Request $request  
     * @param  callable $controller 
     * @param  array $parameters
     * @return array 
     */
    protected function doGetArguments(Request $request, $controller, array $parameters)
    {
        $attributes = $request->attributes->all();
        $arguments = array();
        foreach ($parameters as $param) {
            if (array_key_exists($param->name, $attributes)) {
                $arguments[] = $attributes[$param->name];
            } elseif ($param->getClass() && $param->getClass()->isInstance($request)) {
                $arguments[] = $request;
            } elseif ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
            } else {
                if (is_array($controller)) {
                    $repr = sprintf('%s::%s()', get_class($controller[0]), $controller[1]);
                } elseif (is_object($controller)) {
                    $repr = get_class($controller);
                } else {
                    $repr = $controller;
                }

                throw new \RuntimeException(sprintf('Controller "%s" requires that you provide a value for the "$%s" argument (because there is no default value or because there is a non optional argument after this one).', $repr, $param->name));
            }
        }

        return $arguments;
    }
}