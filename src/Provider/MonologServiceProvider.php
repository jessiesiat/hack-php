<?php

namespace Hack\Provider;

use Hack\Application;
use Hack\ServiceProviderInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

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
			$formatter = new LineFormatter(null, null, true, true);
			$stream = new StreamHandler($c['path.storage'].'/'.$c['monolog.logger.file'], Logger::WARNING);
			$stream->setFormatter($formatter);

			return $stream;
		};
	}

	public function boot(Application $app) {}

}