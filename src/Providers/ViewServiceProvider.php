<?php

namespace Hack\Providers;

use Pimple\Container;
use Twig_Loader_Filesystem;
use Twig_Environment;

class ViewServiceProvider implements \Pimple\ServiceProviderInterface
{
	/**
	 * Register a service into the container
	 *
	 * @param Pimple\Container $app  DI container instance
	 */
	public function register(Container $app)
	{
		$app['view'] = function ($app) {
			$viewPath = $app['config']['view.path'];
			$viewCachePath =$app['config']['view.cache_path'];
			$loader = new Twig_Loader_Filesystem($viewPath);

			return new Twig_Environment($loader, array(
			    'cache' => $viewCachePath,
			    'debug' => true,
			));
		};
	}
}