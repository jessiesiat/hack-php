<?php

namespace Hack\Bootstrapper;

use Hack\Application;

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