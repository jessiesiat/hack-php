<?php

if ( ! function_exists('env'))
{
	/**
	 * Gets the value of an environment variable. Supports boolean, empty and null.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	function env($key, $default = false)
	{
		$value = getenv($key);

		if ($value === false) return $default;

		switch (strtolower($value))
		{
			case 'true':
			case '(true)':
				return true;

			case 'false':
			case '(false)':
				return false;

			case 'null':
			case '(null)':
				return null;

			case 'empty':
			case '(empty)':
				return '';
		}

		return $value;
	}
}
