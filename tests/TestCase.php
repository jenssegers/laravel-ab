<?php

use Jenssegers\AB\Support\Helpers;

class TestCase extends Orchestra\Testbench\TestCase {

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

        $experiments = ['a', 'b', 'c'];
        $goals       = ['register', 'buy', 'contact'];
        $connection  = 'sqlite';

        if (Helpers::isLaravelVersion('4'))
        {
            // Add some experiments.
            Config::set('ab::experiments', $experiments);
            Config::set('ab::goals', $goals);
            Config::set('ab::connection', $connection);
        }
        else
        {
            Config::set('ab.experiments', $experiments);
            Config::set('ab.goals', $goals);
            Config::set('ab.connection', $connection);
        }

        // Make sure we're working in memory.
        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');

        $this->startSession();
    }

    public function tearDown()
    {
        Mockery::close();
        $this->app['session']->flush();
    }

}
