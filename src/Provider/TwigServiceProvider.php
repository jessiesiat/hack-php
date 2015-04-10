<?php

namespace Hack\Provider;

use Hack\ServiceProviderInterface;
use Hack\Application;
use Twig_Loader_Filesystem;
use Twig_Environment;

class TwigServiceProvider implements ServiceProviderInterface
{
	/**
	 * Register a service into the container
	 *
	 * @param Pimple\Container $app  DI container instance
	 */
	public function register(Application $app)
	{
		$app['view'] = function ($app) {
			$viewPath = $app['view.path'];
			$viewCachePath =$app['view.cache.path'];
			$loader = new Twig_Loader_Filesystem($viewPath);

			$twig = new Twig_Environment($loader, array(
			    'cache' => $viewCachePath,
			    'debug' => true,
			));
			$twig->addGlobal('app', $app);

			if (isset($app['debug']) && $app['debug']) {
                $twig->addExtension(new \Twig_Extension_Debug());
            }
            
			return $twig;
		};
	}

	public function boot(Application $app) {}
	
}