<?php

namespace Hack\Bootstrapper;

use Hack\Application;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Debug\ExceptionHandler as SymfonyExceptionHandler;
use Dotenv;

class DetectEnvironment implements Bootstrapable
{
	/**
	 * Detect the environment using environment variables 
	 *
	 * @param  \Hack\Application $app
	 * @return void
	 */
	public function bootstrap(Application $app)
	{
		Dotenv::load($app['path.base']);

		$app['env'] = env('APP_ENV', 'production');
	}

}