<?php namespace Jenssegers\AB\Commands;

use Jenssegers\AB\Models\Experiment;
use Jenssegers\AB\Models\Goal;

use Config;
use DB;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FlushCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ab:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all A/B testing data.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $connection = Config::get('ab::connection');

        DB::connection($connection)->table('experiments')->delete();
        DB::connection($connection)->table('goals')->delete();

        $this->call('ab:install');

        $this->info('A/B testing data flushed.');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array();
    }

}
