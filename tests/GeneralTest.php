<?php
require_once 'TestCase.php';

use Jenssegers\AB\Tester;
use Jenssegers\AB\Models\Experiment;
use Jenssegers\AB\Models\Goal;
use Jenssegers\AB\Commands\InstallCommand;

class GeneralTest extends TestCase {

    public function setUp()
    {
        parent::setUp();

        // Run the install command.
        Artisan::call('ab:install');
    }

    public function tearDown()
    {
        try
        {
            Experiment::truncate();
            Goal::truncate();
        }
        catch (Exception $e) {}
    }

    public function testConstruct()
    {
        $ab = App::make('ab');

        $this->assertInstanceOf('Jenssegers\AB\Tester', $ab);
        $this->assertInstanceOf('Jenssegers\AB\Session\SessionInterface', $ab->getSession());
    }

    public function testAutoCreateExperiments()
    {
        $ab = App::make('ab');
        $ab->experiment();

        $this->assertEquals(3, Experiment::count());
    }

    public function testExperiment()
    {
        $session = Mockery::mock('Jenssegers\AB\Session\SessionInterface');
        $session->shouldReceive('get')->with('experiment')->andReturn('a');

        $ab = new Tester($session);
        $experiment = $ab->experiment();

        $this->assertEquals('a', $experiment);
        $this->assertEquals($experiment, $ab->getSession()->get('experiment'));
    }

    public function testPageview()
    {
        $session = Mockery::mock('Jenssegers\AB\Session\SessionInterface');
        $session->shouldReceive('get')->with('experiment')->andReturn('a');
        $session->shouldReceive('get')->with('pageview')->andReturn(null)->once();
        $session->shouldReceive('set')->with('pageview', 1)->once();

        $ab = new Tester($session);
        $ab->pageview();

        $this->assertEquals(1, Experiment::find('a')->visitors);
    }

    public function testInteract()
    {
        $session = Mockery::mock('Jenssegers\AB\Session\SessionInterface');
        $session->shouldReceive('get')->with('experiment')->andReturn('a');
        $session->shouldReceive('get')->with('interacted')->andReturn(null)->once();
        $session->shouldReceive('set')->with('interacted', 1)->once();

        $ab = new Tester($session);
        $ab->interact();

        $this->assertEquals(1, Experiment::find('a')->engagement);
    }

    public function testComplete()
    {
        $session = Mockery::mock('Jenssegers\AB\Session\SessionInterface');
        $session->shouldReceive('get')->with('experiment')->andReturn('a');
        $session->shouldReceive('get')->with('completed_register')->andReturn(null)->once();
        $session->shouldReceive('set')->with('completed_register', 1)->once();

        $ab = new Tester($session);
        $ab->complete('register');

        $this->assertEquals(1, Goal::where('name', 'register')->where('experiment', 'a')->first()->count);
    }

    public function testTrackWithoutExperiment()
    {
        $request = Request::instance();

        $ab = App::make('ab');
        $ab->track($request);

        $this->assertEquals(0, Experiment::find('a')->visitors);
        $this->assertEquals(0, Experiment::find('a')->engagement);
    }

    public function testTrackWithExperiment()
    {
        $request = Request::instance();

        $ab = App::make('ab');
        $ab->experiment();
        $ab->track($request);

        $this->assertEquals(1, Experiment::find('a')->visitors);
        $this->assertEquals(0, Experiment::find('a')->engagement);
    }

    public function testTrackEngagement()
    {
        $headers = Request::instance()->server->getHeaders();
        $headers['HTTP_REFERER'] = 'http://localhost';
        $request = Request::create('http://localhost/info', 'get', [], [], [], $headers);

        $ab = App::make('ab');
        $ab->experiment();
        $ab->track($request);

        $this->assertEquals(1, Experiment::find('a')->visitors);
        $this->assertEquals(1, Experiment::find('a')->engagement);
    }

    public function testTrackGoal()
    {
        $headers = Request::instance()->server->getHeaders();
        $headers['HTTP_REFERER'] = 'http://localhost';
        $request = Request::create('http://localhost/buy', 'get', [], [], [], $headers);

        $ab = App::make('ab');
        $ab->experiment();
        $ab->track($request);

        $this->assertEquals(1, Experiment::find('a')->visitors);
        $this->assertEquals(1, Experiment::find('a')->engagement);
        $this->assertEquals(1, Goal::where('name', 'buy')->where('experiment', 'a')->first()->count);
    }

}
