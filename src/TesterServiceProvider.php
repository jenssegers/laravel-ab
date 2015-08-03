<?php namespace Jenssegers\AB;

use Jenssegers\AB\Session\LaravelSession;
use Jenssegers\AB\Session\CookieSession;

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
        $this->loadViewsFrom(realpath(__DIR__), 'ab');
        
        $this->publishes([
            realpath(__DIR__).'/config/config.php' => config_path('ab.php'),
        ]);
    }
    
    /**
     * Register the application events.
     *
     * @return void
     */
    public static function setTrack($request) {
        $service = new TesterServiceProvider;
        $service->app['ab']->track($request);
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

        $this->registerCommands();
    }

    /**
     * Register Artisan commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        // Available commands.
        $commands = ['install', 'flush', 'report', 'export'];

        // Bind the command objects.
        foreach ($commands as &$command)
        {
            $class = 'Jenssegers\\AB\\Commands\\' . ucfirst($command) . 'Command';
            $command = "ab::command.$class";

            $this->app->bind($command, function($app) use ($class)
            {
                return new $class();
            });
        }

        // Register artisan commands.
        $this->commands($commands);
    }

}
