<?php

namespace Hack\Filesystem;

class Filesystem 
{
	
	public function __construct()
	{
		// 
	}

	public function get($path)
	{
		return file_get_contents($path);
	}

	public function getRequire($path)
	{
		return require $path;
	}

}