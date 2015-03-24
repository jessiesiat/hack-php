<?php

namespace Hack\Bootstrapper;

use Hack\Foundation\Application;

class RegisterProviders implements Bootstrapable
{
	/**
	 * {@inheritdoc}
	 */
	public function bootstrap(Application $app)
	{
		$app->registerProviders();
	}
}