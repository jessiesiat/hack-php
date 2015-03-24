<?php

namespace Hack\Bootstrapper;

use Hack\Foundation\Application;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Config\Repository;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Finder;

class LoadConfiguration implements Bootstrapable
{
	/**
	 * Load the configuration options for use by the application
	 *
	 * @param $app  Hack\Foundation\Application
	 */
	public function bootstrap(Application $app)
	{
		$app['config'] = $config = new Repository;

		$this->loadConfigurationFiles($app, $config);
	}

	/**
	 * Load the configuration items from all of the files.
	 *
	 * @param  \Illuminate\Contracts\Foundation\Application  $app
	 * @param  \Illuminate\Contracts\Config\Repository  $config
	 * @return void
	 */
	protected function loadConfigurationFiles(Application $app, RepositoryContract $config)
	{
		foreach ($this->getConfigurationFiles($app) as $key => $path)
		{
			$config->set($key, require $path);
		}
	}

	/**
	 * Get all of the configuration files for the application.
	 *
	 * @param  \Illuminate\Contracts\Foundation\Application  $app
	 * @return array
	 */
	protected function getConfigurationFiles(Application $app)
	{
		$files = [];
		$configPath = $app['path.config'];

		foreach (Finder::create()->files()->name('*.php')->in($configPath) as $file)
		{
			$nesting = $this->getConfigurationNesting($file, $configPath);

			$files[$nesting.basename($file->getRealPath(), '.php')] = $file->getRealPath();
		}

		return $files;
	}

	/**
	 * Get the configuration file nesting path.
	 *
	 * @param  \Symfony\Component\Finder\SplFileInfo  $file
	 * @return string
	 */
	private function getConfigurationNesting(SplFileInfo $file, $configPath)
	{
		$directory = dirname($file->getRealPath());

		if ($tree = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR))
		{
			$tree = str_replace(DIRECTORY_SEPARATOR, '.', $tree).'.';
		}

		return $tree;
	}
}