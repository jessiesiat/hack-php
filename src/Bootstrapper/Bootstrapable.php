<?php

namespace Hack\Bootstrapper;

use Hack\Foundation\Application;

interface Bootstrapable
{
	/**
	 * Bootstrap an object for use with the application
	 *
	 * @param $app  Hack\Foundation\Application
	 */
	public function bootstrap(Application $app);
}