<?php namespace Jenssegers\AB\Support;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Application;

class Helpers {

	/**
     * Determine if laravel starts with any of the given version strings
     * 
     * @param  string|array  $startsWith
     * @return boolean
     */
    public static function isLaravelVersion($startsWith)
    {
        return strpos(Application::VERSION, $startsWith) === 0;
    }

	/**
	 * Convert to array list.
	 *
	 * @param  mixed $value
	 * @return array
	 */
	public static function lists($value)
	{
		if ($value instanceof Collection)
		{
			return $value->all();
		}

		return $value;
	}

}
