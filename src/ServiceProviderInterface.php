<?php

namespace Hack;

use Hack\Foundation\Application;

interface ServiceProviderInterface {
	
	/**
	 * Register a service into the application container.
	 */
	public function register(Application $app);

	/**
	 * Configure a service, that is before handling the request/response
	 */
	public function boot(Application $app);

}