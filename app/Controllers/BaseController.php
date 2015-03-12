<?php 

namespace App\Controllers;

use Twig_Loader_Filesystem;
use Twig_Environment;
use Hack\Application;

abstract class BaseController {

	/**
	 * The application instance being facaded.
	 *
	 * @var \Hack\Application
	 */
	protected static $app;

	public static function setApplication(Application $app)
	{
		static::$app = $app;
	}

	public function render($view, $data = array())
	{
		$viewPath = static::$app['config']['view.path'];
		$viewCachePath = static::$app['config']['view.cache_path'];

		$loader = new Twig_Loader_Filesystem($viewPath);
		$twig = new Twig_Environment($loader, array(
		    'cache' => $viewCachePath,
		    'debug' => true,
		));

		return $twig->render($view, $data);
	}

}