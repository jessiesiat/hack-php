<?php

namespace Hack\Bootstrapper;

use Hack\Foundation\Listeners\StringResponseListener;
use Hack\Foundation\Application;
use Hack\ControllerFactory;
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

class SetKernelDefinition implements Bootstrapable
{
	/**
	 * {@inheritdoc}
	 */
	public function bootstrap(Application $app)
	{
		$app['resolver'] = function($c) {
			return new ControllerResolver;
		};
		$app['routes'] = function($c) {
			return new RouteCollection;
		};
		$app['controllers'] = function($c) {
			return new ControllerFactory($c['routes']);
		};
		$app['request_context'] = function($c) {
			return new RequestContext;
		};
		$app['dispatcher'] = function($c) {
			$matcher = new UrlMatcher($c['routes'], $c['request_context']);
			$dispatcher = new EventDispatcher();
			$dispatcher->addSubscriber(new StringResponseListener);
			$dispatcher->addSubscriber(new RouterListener($matcher));
			return $dispatcher;
		};
		$app['kernel'] = function($c) {
			return new HttpKernel($c['dispatcher'], $c['resolver']);
		};
	}
}