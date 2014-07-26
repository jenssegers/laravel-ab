<?php
require_once 'TestCase.php';

use Jenssegers\AB\Session\LaravelSession;
use Jenssegers\AB\Session\CookieSession;

class SessionTest extends TestCase {

    public function testLaravelSession()
    {
        $session = new LaravelSession;
        $session->set('foo', 'bar');
        $session->set('bar', 1);

        $this->assertEquals('bar', $session->get('foo'));
        $this->assertEquals(1, $session->get('bar'));

        $this->assertEquals('bar', Session::get('ab.foo'));
        $this->assertEquals(1, Session::get('ab.bar'));
    }

    public function testCookieSession()
    {
        Cookie::shouldReceive('make')->passthru();
        Cookie::shouldReceive('queue')->with('ab', array('foo'=>'bar'), 60)->once()->passthru();
        Cookie::shouldReceive('queue')->with('ab', array('foo'=>'bar','bar'=>1), 60)->once()->passthru();

        $session = new CookieSession;
        $session->set('foo', 'bar');
        $session->set('bar', 1);

        $this->assertEquals('bar', $session->get('foo'));
        $this->assertEquals(1, $session->get('bar'));
    }

}
