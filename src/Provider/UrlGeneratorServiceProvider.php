<?php

namespace Hack\Provider;

use Hack\Application;
use Hack\ServiceProviderInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;

class UrlGeneratorServiceProvider implements ServiceProviderInterface
{
	/**
	 * Register a service into the container
	 *
	 * @param \Hack\Application $app  DI container instance
	 */
	public function register(Application $app)
	{
		$app['url.generator'] = function($app) {
			return new UrlGenerator($app['routes'], $app['request_context']);			
		};
	}

	public function boot(Application $app) {}
	
}