<?php

namespace Hack\Bootstrapper;

use Hack\Application;

interface Bootstrapable
{
	/**
	 * Bootstrap an object for use with the application
	 *
	 * @param $app  \Hack\Application
	 */
	public function bootstrap(Application $app);
}