<?php namespace Jenssegers\AB\Facades;

use Illuminate\Support\Facades\Facade;

class AB extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'ab'; }

}
