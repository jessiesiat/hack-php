<?php 

namespace Hack\Foundation;

use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Config\FileLocator;
use Hack\Controller as BaseController;
use Hack\Config\Repository as ConfigRepository;
use Hack\Foundation\Listeners\StringResponseListener;
use Pimple\ServiceProviderInterface;

class Application extends \Pimple\Container
{
	protected $kernel;
	
	/**
     * Instantiate a new Application. Registers base services / parameters into the 
     * DI container. Also acccepts array of key value pairs to be registered on
     * the container.
     *
     * @param array $items 	The parameters or objects.
     */
	public function __construct($items = array())
	{
		$this['dispatcher'] = function($c) {
			return new EventDispatcher;	
		};
		$this['path.app'] = realpath(__DIR__.'/../../app');
		$this['path.base'] = realpath(__DIR__.'/../..');
		$this['path.config'] = realpath(__DIR__.'/../../config');
		$this['config'] = function($c) {
			return (new ConfigRepository)->loadFromPath($c['path.config']);
		};
		$this['debug'] = $this['config']['app.debug'];
		$this->extend('dispatcher', function($dispatcher, $c) {
			$dispatcher->addSubscriber(new StringResponseListener);
			return $dispatcher;
		});
		$this['resolver'] = function($c) {
			return new ControllerResolver;
		};

		BaseController::setApplication($this);
		if($this['debug']) \Symfony\Component\Debug\Debug::enable();
		
		date_default_timezone_set($this['config']['app.timezone']);

		foreach ($items as $key => $value) {
			$this[$key] = $value;
		}

		$this->registerServiceProviders();
	}

	/**
	 * Handles the Request and sends back the Response
	 *
	 * @param Symfony\Component\HttpFoundation\Request  $request
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function handle(Request $request)
	{
		$routes = $this->bootRoutes();

		$context = (new RequestContext())->fromRequest($request);

		$matcher = new UrlMatcher($routes, $context);

		$this['dispatcher']->addSubscriber(new RouterListener($matcher));

		$this->kernel = new HttpKernel($this['dispatcher'], $this['resolver']);

		return $this->kernel->handle($request);
	}

	public function terminate(Request $request, Response $response)
	{
		$this->kernel->terminate($request, $response);
	}

	public function bootRoutes()
	{
		return require $this['path.app'].'/routes.php';
	}

	/**
	 * Registers core service providers into the container.
	 * Service providers are found in providers array in 
	 * app config path.
	 */
	public function registerServiceProviders()
	{
		$providers = $this['config']['app.providers'];

		// Loop through each provider, create an instance and 
		// register it in the container 
		foreach ($providers as $provider) 
		{
			$object = $this->make($provider);

			if($object instanceOf ServiceProviderInterface) {
				$this->register($object);
			}
		}
	}

	/**
	 * Create an instance of the passed argument
	 *
	 * @param mixed $abstract  
	 * @return Object  
	 */
	public function make($abstract)
	{
		$reflector = new \ReflectionClass($abstract);

		if (! $reflector->isInstantiable()) {
			throw new \Exception("Cannot resolve class $abstract");
		}

		$constructor = $reflector->getConstructor();

		if(is_null($constructor)) {
			return $reflector->newInstance();
		}

		$dependencies = $constructor->getParameters();

		$instances = $this->getDependencies($dependencies);

		return $reflector->newInstanceArgs($instances);
	}

	/**
	 * Resolve all of the dependencies from the ReflectionParameters.
	 *
	 * @param  array  $parameters
	 * @param  array  $primitives
	 * @return array
	 */
	protected function getDependencies($parameters)
	{
		$dependencies = [];

		foreach ($parameters as $parameter)
		{
			$dependency = $parameter->getClass();

			// If dependency is null, we'll assume it is a primitive type.
			// If we cannot resolve it, we will just bomb out since we 
			// have no where to go.
			if (is_null($dependency))
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

		throw new Exception($message);
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
		catch (Exception $e)
		{
			if ($parameter->isOptional())
			{
				return $parameter->getDefaultValue();
			}

			throw $e;
		}
	}

}