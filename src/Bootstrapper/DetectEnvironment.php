<?php

namespace Hack\Bootstrapper;

use Hack\Application;
use Symfony\Component\Debug\Debug;

class DetectEnvironment implements Bootstrapable
{
	/**
	 * {@inheritdoc}
	 */
	public function bootstrap(Application $app)
	{
		$debug = $app['config']['app.debug'];

		if($debug) Debug::enable();
	}
}