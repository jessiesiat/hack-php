<?php

namespace Hack\Provider;

use Hack\ServiceProviderInterface;
use Hack\Application;
use Hack\EventListener\LogListener;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class MonologServiceProvider implements ServiceProviderInterface
{

	public function register(Application $app)
	{
		$app['monolog.logger'] = function($c) {
			$logger = new Logger($c['monolog.logger.name']);
			$logger->pushHandler($c['monolog.handler.default']);

			return $logger;
		};
		$app['monolog.logger.name'] = 'hack-php';
		$app['monolog.logger.file'] = 'hack-php.log';
		$app['monolog.handler.default'] = function($c) {
			return new StreamHandler($c['path.storage'].'/'.$c['monolog.logger.file'], Logger::WARNING);
		};
	}

	public function boot(Application $app) {}

}