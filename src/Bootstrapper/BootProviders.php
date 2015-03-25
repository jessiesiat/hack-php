<?php

namespace Hack\Bootstrapper;

use Hack\Application;

class BootProviders implements Bootstrapable
{
	/**
	 * {@inheritdoc}
	 */
	public function bootstrap(Application $app)
	{
		$app->bootProviders();
	}
}