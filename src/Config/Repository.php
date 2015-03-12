<?php 

namespace Hack\Config;

use Symfony\Component\Finder\Finder;

class Repository implements \ArrayAccess
{

	protected $items;
	
	public function __construct($items = array())
	{
		$this->items = $items;
	}

	public function set($key, $value = null)
	{
		array_set($this->items, $key, $value);
	}

	public function get($key, $default = null)
	{
		return array_get($this->items, $key, $default);
	}

	public function loadFromPath($path)
	{
		foreach(Finder::create()->files()->name('*.php')->in($path) as $file) 
		{
			$this->set(basename($file, '.php'), require $file->getRealPath());
		}

		return $this;
	}

	public function offsetExists($key)
	{
		return $this->get($key) !== null;
	}

	public function offsetGet($key)
	{
		return $this->get($key);
	}

	public function offsetSet($key, $value)
	{
		$this->set($key, $value);
	}

	public function offsetUnset($key)
	{
		// do nothing
	}

}