<?php

use Jenssegers\AB\Models\Experiment;
use Jenssegers\AB\Models\Goal;
use Jenssegers\AB\Commands\InstallCommand;

class GeneralTest extends Orchestra\Testbench\TestCase {

    protected function getPackageProviders()
    {
        return ['Jenssegers\AB\TesterServiceProvider'];
    }

    protected function getPackageAliases()
    {
        return ['AB' => 'Jenssegers\AB\Facades\AB'];
    }

    public function setUp()
    {
        parent::setUp();

        // Add some experiments.
        Config::set('ab::experiments', ['a', 'b', 'c']);
        Config::set('ab::goals', ['register', 'buy', 'contact']);

        // Make sure we're working in memory.
        Config::set('database.connections.sqlite.database', ':memory:');
        Config::set('ab::connection', 'sqlite');

        // Run the install command.
        Artisan::call('ab:install');

        $this->startSession();
    }

    public function tearDown()
    {
        try {
            Experiment::truncate();
            Goal::truncate();
        }
        catch (Exception $e) {}

        $this->flushSession();
    }

    public function testConstruct()
    {
        $ab = App::make('ab');

        $this->assertInstanceOf('Jenssegers\AB\Tester', $ab);
    }

    public function testExperiment()
    {
        $ab = App::make('ab');

        $this->assertNotNull($ab->experiment());
        $this->assertTrue(in_array($ab->experiment(), ['a', 'b', 'c']));
        $this->assertEquals($ab->experiment(), Session::get('ab.experiment'));
        $this->assertEquals(1, Experiment::find($ab->experiment())->visitors);
    }

    public function testInteract()
    {
        $ab = App::make('ab');
        $ab->setExperiment('a');
        $ab->interact();

        $this->assertEquals(1, Experiment::find('a')->engagement);
        $this->assertEquals(0, Experiment::find('b')->engagement);
        $this->assertEquals(0, Experiment::find('c')->engagement);
    }

    public function testComplete()
    {
        $ab = App::make('ab');
        $ab->setExperiment('a');
        $ab->complete('register');

        $this->assertEquals(1, Goal::where('name', 'register')->where('experiment', 'a')->first()->count);
        $this->assertNull(Goal::where('name', 'register')->where('experiment', 'b')->first());
        $this->assertNull(Goal::where('name', 'register')->where('experiment', 'c')->first());
    }

}
