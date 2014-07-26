<?php namespace Jenssegers\AB\Session;

use Illuminate\Support\Facades\Cookie;

class CookieSession implements SessionInterface {

    /**
     * The name of the cookie.
     *
     * @var string
     */
    protected $cookieName = 'ab';

    /**
     * A copy of the cookie data.
     *
     * @var array
     */
    protected $cookieData = null;

    /**
     * Cookie lifetime.
     *
     * @var integer
     */
    protected $minutes = 60;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->cookieData = Cookie::get($this->cookieName, []);
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, $default = null)
    {
        return array_get($this->cookieData, $name, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        $this->cookieData[$name] = $value;

        return Cookie::queue($this->cookieName, $this->cookieData, $this->minutes);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->cookieData = [];

        return Cookie::forget($this->cookieName);
    }

}
