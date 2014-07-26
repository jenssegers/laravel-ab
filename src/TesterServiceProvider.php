<?php namespace Jenssegers\AB;

use Jenssegers\AB\Session\LaravelSession;
use Jenssegers\AB\Session\CookieSession;
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

        // Start the A/B tracking when routing starts.
        $this->app->before(function($request)
        {
            $this->app['ab']->track($request);
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
            return new Tester(new CookieSession);
        });

        // Register artisan commands.
        $this->app['ab.commands.install'] = new InstallCommand;
        $this->app['ab.commands.flush'] = new FlushCommand;
        $this->app['ab.commands.report'] = new ReportCommand;
        $this->commands('ab.commands.install', 'ab.commands.flush', 'ab.commands.report');
    }

}
