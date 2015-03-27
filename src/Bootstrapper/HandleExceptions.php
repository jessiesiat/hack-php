<?php 

namespace Hack\Bootstrapper;

use Hack\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Debug\ExceptionHandler as SymfonyExceptionHandler;

class HandleExceptions implements Bootstrapable
{
	/**
	 * Application instance
	 *
	 * @var Hack\Application
	 */
	protected $app;

	/**
	 * Define exception handler
	 *
	 * @param  \Hack\Application $app
	 * @return void
	 */
	public function bootstrap(Application $app)
	{
		$this->app = $app;

		error_reporting(-1);

		set_error_handler([$this, 'handleError']);

		set_exception_handler([$this, 'handleException']);

		register_shutdown_function([$this, 'handleShutdown']);

		if ($this->app['env'] != 'testing')
		{
			ini_set('display_errors', 'Off');
		}
	}

	/**
	 * Error handler
	 *
	 * @param  int  $level
	 * @param  string  $message
	 * @param  string  $file
	 * @param  int  $line
	 * @return void
	 *
	 * @throws \ErrorException
	 */
	public function handleError($level, $message, $file, $line)
	{
		if (error_reporting() & $level) {
			// this will then be handled by `set_exception_handler`
		    throw new \ErrorException($message, 0, $level, $file, $line);    
	    }
	}

	/**
	 * Exception handler. Handle uncaught Exception from the application
	 *
	 * @param  \Exception $e
	 * @return void
	 */
	public function handleException($e)
	{
		// log the error
		$this->app['monolog.logger']->error($e);

		// check to see if app is running in console/http to create proper response
		if (php_sapi_name() == 'cli') {
		    return (new ConsoleApplication)->renderException($e, new ConsoleOutput);
		} else {
			return (new SymfonyExceptionHandler(env('APP_DEBUG')))
							->createResponse($e)
							->send();
		}
	}

	/**
	 * Shutdown handler
	 * 
	 * @return void
	 */
	public function handleShutdown()
	{
		if ( ! is_null($error = error_get_last()) && $this->isFatal($error['type']))
		{
			// will be handled back by `@handleException`
			$this->handleException($this->fatalExceptionFromError($error));
		}
	}

	/**
	 * Create instance of FatalErrorException
	 *
	 * @param  array $error
	 * @return \Symfony\Component\Debug\Exception\FatalErrorException
	 */
	protected function fatalExceptionFromError(array $error)
	{
		return new FatalErrorException(
			$error['message'], $error['type'], 0, $error['file'], $error['line']
		);
	}

	/**
	 * Determine if the error type is a PHP fatal error
	 *
	 * @param  int $type
	 * @return bool
	 */
	protected function isFatal($type)
	{
		return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
	}

}