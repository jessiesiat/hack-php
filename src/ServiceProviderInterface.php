<?php

namespace Hack;

use Hack\Application;

interface ServiceProviderInterface {
	
	/**
	 * Register a service into the application container.
	 *
	 * @param $app  Hack\Application
	 */
	public function register(Application $app);

	/**
	 * Configure a service, that is before handling the request/response
	 * Called after all the services are registered in the application
	 *
	 * @param $app  Hack\Application
	 */
	public function boot(Application $app);

}