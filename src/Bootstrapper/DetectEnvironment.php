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

		$app['env'] = getenv('APP_ENV') ?: 'production';

		if ($this->isDebugOn()) {
			Debug::enable();	
		} else {
			set_exception_handler([$this, 'exceptionHandler']);
		}
	}

	/** 
	 * Exception handler if debug is off
	 *
	 * @param  \Exception $e
	 * @return \Symfony\Component\HttpFoundation\Response 
	 */
	public function exceptionHandler(\Exception $e)
	{
		if ($e instanceof HttpExceptionInterface)
		{
			// render custom view here if exists base on status code
		}

		return (new SymfonyExceptionHandler)->createResponse($e)->send();
	}

	/**
	 * Determines whether debug is on by looking into environment 
	 * variable APP_DEBUG
	 *
	 * @param  bool|mixed $default  Default if env. variable is not found
	 * @return bool|mixed
	 */
	public function isDebugOn($default = false)
	{
		$debug = getenv('APP_DEBUG');

		if (false === $debug) return $default;

		switch (strtolower($debug)) 
		{
			case 'true':
			case '(true)':
				return true;
			case 'false':
			case '(false)': 
				return false;
			default:
				return false;
		}
	}
}