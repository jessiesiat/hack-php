<?php

namespace Hack\Bootstrapper;

use Hack\Application;

interface Bootstrapable
{
	/**
	 * Bootstrap an object for use with the application
	 *
	 * @param $app  Hack\Foundation\Application
	 */
	public function bootstrap(Application $app);
}