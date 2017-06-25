<?php namespace Jenssegers\AB\Commands;

use Jenssegers\AB\Models\Experiment;
use Jenssegers\AB\Models\Goal;

use Config;
use Schema;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class InstallCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ab:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepare the A/B testing database.';

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

        // Create experiments table.
        if ( ! Schema::connection($connection)->hasTable('experiments'))
        {
            Schema::connection($connection)->create('experiments', function($table)
            {
                $table->increments('id');
                $table->string('name');
                $table->integer('visitors')->unsigned()->default(0);
                $table->integer('engagement')->unsigned()->default(0);
                $table->timestamps();
            });
        }

        // Create goals table.
        if ( ! Schema::connection($connection)->hasTable('goals'))
        {
            Schema::connection($connection)->create('goals', function($table)
            {
                $table->increments('id');
                $table->string('name');
                $table->string('experiment');
                $table->integer('count')->unsigned()->default(0);
                $table->timestamps();
            });
        }

        $this->info('Database schema initialized.');

        $experiments = Config::get('ab')['experiments'];

        if ( ! $experiments or empty($experiments))
        {
            return $this->error('No experiments configured.');
        }

        $goals = Config::get('ab')['goals'];

        if ( ! $goals or empty($goals))
        {
            return $this->error('No goals configured.');
        }

        // Populate experiments and goals.
        foreach ($experiments as $experiment)
        {
            Experiment::firstOrCreate(['name' => $experiment]);

            foreach ($goals as $goal)
            {
                Goal::firstOrCreate(['name' => $goal, 'experiment' => $experiment]);
            }
        }

        $this->info('Added ' . count($experiments) . ' experiments.');
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
