<?php 

namespace Hack\Foundation;

use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Config\FileLocator;
use Hack\Foundation\Listeners\StringResponseListener;
use Hack\Config\Repository as ConfigRepository;
use Hack\Controller as BaseController;
use Hack\ServiceProviderInterface;
use Hack\ControllerFactory;

class Application extends \Pimple\Container
{
	protected $booted = false;

	protected $providers = array();
	
	/**
     * Instantiate a new Application. Registers base services / parameters into the 
     * DI container. Acccepts array of key value pairs to be registered on
     * the container.
     *
     * @param array $items 	The parameters or objects.
     */
	public function __construct($items = array())
	{
		parent::__construct($items);

		$this['path.app'] = realpath(__DIR__.'/../../app');
		$this['path.base'] = realpath(__DIR__.'/../..');
		$this['path.config'] = realpath(__DIR__.'/../../config');
		$this['path.storage'] = realpath(__DIR__.'/../../storage');
		$this['config'] = function($c) {
			return (new ConfigRepository)->loadFromPath($c['path.config']);
		};
		$this['debug'] = $this['config']['app.debug'];
		
		$this['resolver'] = function($c) {
			return new ControllerResolver;
		};
		$this['routes'] = function($c) {
			return new RouteCollection;
		};
		$this['controllers'] = function($c) {
			return new ControllerFactory($c['routes']);
		};
		$this['request_context'] = function($c) {
			return new RequestContext;
		};
		$this['dispatcher'] = function($c) {
			$matcher = new UrlMatcher($this['routes'], $this['request_context']);
			$dispatcher = new EventDispatcher();
			$dispatcher->addSubscriber(new StringResponseListener);
			$dispatcher->addSubscriber(new RouterListener($matcher));
			return $dispatcher;
		};
		$this['kernel'] = function($c) {
			return new HttpKernel($this['dispatcher'], $this['resolver']);
		};
		BaseController::setApplication($this);
		if($this['debug']) \Symfony\Component\Debug\Debug::enable();
		
		date_default_timezone_set($this['config']['app.timezone']);

		$this->registerProviders();
	}

	/**
	 * Registers core service providers into the container.
	 * Service providers are found in providers array in 
	 * app config path.
	 */
	public function registerProviders()
	{
		$providers = $this['config']['app.providers'];

		// Loop through each provider, create an instance and 
		// register it in the container 
		foreach ($providers as $provider) 
		{
			$this->providers[] = $object = $this->make($provider);

			if(!$object instanceOf ServiceProviderInterface) {
				throw new \Exception('Service provider %s must implement Hack\ServiceProviderInterface', get_class($object));
			}

			$object->register($this);
		}
	}

	/**
	 * Runs the application
	 * 
	 * @param Symfony\Component\HttpFoundation\Request  $request
	 */
	public function run(Request $request = null)
	{
		if (null == $request) {
			$request = Request::createFromGlobals();
		}

		$response = $this->handle($request);
		$response->send();
		$this->terminate($request, $response);
	}

	/**
	 * Handles the Request and sends back the Response
	 *
	 * @param Symfony\Component\HttpFoundation\Request  $request
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function handle(Request $request)
	{
		$this['request_context']->fromRequest($request);

		$this->boot();

		return $this['kernel']->handle($request);
	}

	public function boot()
	{
		if ($this->booted) return;

		foreach ($this->providers as $provider) {
			$provider->boot($this);
		}
		$this->booted = true;
	}

	/**
	 * Terminates the application. Fires `kernel.terminate` event
	 * 
	 * @param Symfony\Component\HttpFoundation\Request  $request
	 * @param Symfony\Component\HttpFoundation\Response  $response
	 */
	public function terminate(Request $request, Response $response)
	{
		$this['kernel']->terminate($request, $response);
	}

	/**
	 * Add GET route to the application
	 *
	 * @param mixed  $pattern  Route path
	 * @param mixed  $to       Controller handler
	 */
	public function get($pattern, $to = null)
	{
		$this['controllers']->match('GET', $pattern, $to);
	}

	/**
	 * Add POST route to the application
	 *
	 * @param mixed  $pattern  Route path
	 * @param mixed  $to       Controller handler
	 */
	public function post($pattern, $to = null)
	{
		$this['controllers']->match('POST', $pattern, $to);
	}

	/**
	 * Add PUT route to the application
	 *
	 * @param mixed  $pattern  Route path
	 * @param mixed  $to       Controller handler
	 */
	public function put($pattern, $to = null)
	{
		$this['controllers']->match('PUT', $pattern, $to);
	}

	/**
	 * Add PATCH route to the application
	 *
	 * @param mixed  $pattern  Route path
	 * @param mixed  $to       Controller handler
	 */
	public function patch($pattern, $to = null)
	{
		$this['controllers']->match('PATCH', $pattern, $to);
	}

	/**
	 * Add DELETE route to the application
	 *
	 * @param mixed  $pattern  Route path
	 * @param mixed  $to       Controller handler
	 */
	public function delete($pattern, $to = null)
	{
		$this['controllers']->match('DELETE', $pattern, $to);
	}

	/** 
	 * Flushes the routes to the application
	 */
	public function flushRoutes()
	{
		$this['routes']->addCollection($this['controllers']->allRoutes());
	}

	/**
	 * Create an instance of the passed argument
	 *
	 * @param mixed  $abstract  
	 * @param array  $parameters
	 * @return Object  
	 */
	public function make($abstract, $parameters = [])
	{
		if ($abstract instanceof Closure)
		{
			return $abstract($this, $parameters);
		}

		$reflector = new \ReflectionClass($abstract);

		if (! $reflector->isInstantiable()) {
			throw new \Exception("Cannot resolve class $abstract");
		}

		$constructor = $reflector->getConstructor();

		if(is_null($constructor)) {
			return $reflector->newInstance();
		}

		$dependencies = $constructor->getParameters();

		// Key the parameters by name if it has a numeric key base on the dependencies
		$parameters = $this->keyParametersByArgument(
			$dependencies, $parameters
		);

		$instances = $this->getDependencies($dependencies, $parameters);

		return $reflector->newInstanceArgs($instances);
	}

	/**
	 * Resolve all of the dependencies from the ReflectionParameters.
	 *
	 * @param  array  $parameters
	 * @param  array  $primitives
	 * @return array
	 */
	protected function getDependencies($parameters, array $primitives = [])
	{
		$dependencies = [];

		foreach ($parameters as $parameter)
		{
			$dependency = $parameter->getClass();

			// Assign passed primitive types if exist on parameters, 
			// else resolve parameter. If we cannot resolve we will
			// just bomb out since we have no where to go!
			if (array_key_exists($parameter->name, $primitives))
			{
				$dependencies[] = $primitives[$parameter->name];
			}
			elseif (is_null($dependency))
			{
				$dependencies[] = $this->resolveNonClass($parameter);
			}
			else
			{
				$dependencies[] = $this->resolveClass($parameter);
			}
		}

		return (array) $dependencies;
	}

	/**
	 * Resolve a non-class hinted dependency.
	 *
	 * @param  \ReflectionParameter  $parameter
	 * @return mixed
	 *
	 * @throws Exception
	 */
	protected function resolveNonClass(\ReflectionParameter $parameter)
	{
		if ($parameter->isDefaultValueAvailable())
		{
			return $parameter->getDefaultValue();
		}

		$message = "Unresolvable dependency resolving [$parameter] in class {$parameter->getDeclaringClass()->getName()}";

		throw new \Exception($message);
	}

	/**
	 * Resolve a class based dependency from the container.
	 *
	 * @param  \ReflectionParameter  $parameter
	 * @return mixed
	 *
	 * @throws Exception
	 */
	protected function resolveClass(\ReflectionParameter $parameter)
	{
		try
		{
			return $this->make($parameter->getClass()->name);
		}

		// If we can not resolve the class instance, we will check to see if the value
		// is optional, and if it is we will return the optional parameter value as
		// the value of the dependency, similarly to how we do this with scalars.
		catch (\Exception $e)
		{
			if ($parameter->isOptional())
			{
				return $parameter->getDefaultValue();
			}

			throw $e;
		}
	}

	/**
	 * If extra parameters are passed by numeric ID, re-key them by argument name.
	 *
	 * @param  array  $dependencies
	 * @param  array  $parameters
	 * @return array
	 */
	protected function keyParametersByArgument(array $dependencies, array $parameters)
	{
		foreach ($parameters as $key => $value)
		{
			if (is_numeric($key))
			{
				unset($parameters[$key]);

				$parameters[$dependencies[$key]->name] = $value;
			}
		}

		return $parameters;
	}

}