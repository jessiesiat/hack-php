<?php 

namespace Hack;

use Hack\Foundation\Application;
use Twig_Loader_Filesystem;
use Twig_Environment;

abstract class Controller {

	/**
	 * The application instance being facaded.
	 *
	 * @var \Hack\Application
	 */
	protected static $app;

	/**
	 * Set the application instance
	 *
	 * @param Hack\Foundation\Application  $app
	 */
	public static function setApplication(Application $app)
	{
		static::$app = $app;
	}

	/**
	 * Get the application instance
	 *
	 * @return Hack\Foundation\Application
	 */
	public static function getApplication()
	{
		return static::$app;
	}

	/**
	 * Render view using Twig templating library
	 *
	 * @param mixed  $view  Name of view template to render
	 * @param array  $data  Array of data to be pass to the view
	 * @return mixed
	 */
	public function render($view, $data = array())
	{
		return static::$app['view']->render($view, $data);
	}

}