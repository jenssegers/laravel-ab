<?php namespace Jenssegers\AB;

use Jenssegers\AB\Commands\InstallCommand;
use Jenssegers\AB\Commands\FlushCommand;
use Jenssegers\AB\Commands\ReportCommand;

use Illuminate\Support\ServiceProvider;

class TesterServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Fix for PSR-4
        $this->package('jenssegers/ab', 'ab', realpath(__DIR__));

        // Boot the A/B testing when routing starts.
        $this->app->before(function()
        {
            $this->app['ab']->track();
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['ab'] = $this->app->share(function($app)
        {
            return new Tester;
        });

        // Register artisan commands.
        $this->app['ab.commands.install'] = new InstallCommand;
        $this->app['ab.commands.flush'] = new FlushCommand;
        $this->app['ab.commands.report'] = new ReportCommand;
        $this->commands('ab.commands.install', 'ab.commands.flush', 'ab.commands.report');
    }

}
