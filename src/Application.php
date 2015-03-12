<?php 

namespace Hack;

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
use Hack\Config\Repository as ConfigRepository;
use Hack\Controller as BaseController;

class Application extends \Pimple\Container
{
	protected $kernel;
	
	public function __construct($items = array())
	{
		$this['dispatcher'] = function($c) {
			return new EventDispatcher;	
		};
		$this['path.app'] = realpath(__DIR__.'/../app');
		$this['path.base'] = realpath(__DIR__.'/..');
		$this['path.config'] = realpath(__DIR__.'/../config');
		$this['config'] = function($c) {
			return (new ConfigRepository)->loadFromPath($c['path.config']);
		};
		$this['debug'] = $this['config']['app.debug'];
		$this['file_locator'] = function($c) {
			return new FileLocator(array($c['path.app']));	
		};
		$this['php_file_loader'] = function($c) {
			return new PhpFileLoader($c['file_locator']);
		};
		$this->extend('dispatcher', function($dispatcher, $c) {
			$dispatcher->addSubscriber(new \App\Listeners\StringResponseListener);
			return $dispatcher;
		});
		$this['resolver'] = function($c) {
			return new ControllerResolver;
		};

		BaseController::setApplication($this);

		foreach ($items as $key => $value) {
			$this[$key] = $value;
		}
	}

	public function handle(Request $request)
	{
		if($this['debug']) \Symfony\Component\Debug\Debug::enable();

		$routes = $this['php_file_loader']->load('routes.php');

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

}