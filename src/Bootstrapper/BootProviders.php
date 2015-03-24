<?php

namespace Hack\Bootstrapper;

use Hack\Foundation\Application;

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