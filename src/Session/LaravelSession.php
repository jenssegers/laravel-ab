<?php namespace Jenssegers\AB\Session;

use Illuminate\Support\Facades\Session;

class LaravelSession implements SessionInterface {

    /**
     * Session key prefix.
     *
     * @var string
     */
    protected $prefix = 'ab.';

    /**
     * {@inheritdoc}
     */
    public function get($name, $default = null)
    {
        return Session::get($this->prefix . $name, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        return Session::set($this->prefix . $name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return Session::clear();
    }

}
