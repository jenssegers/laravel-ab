<?php

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
