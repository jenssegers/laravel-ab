<?php namespace Jenssegers\AB\Commands;

use Jenssegers\AB\Models\Experiment;
use Jenssegers\AB\Models\Goal;

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
     * AB Tester.
     *
     * @var \Jenssegers\AB\Tester
     */
    protected $ab;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->ab = app('ab');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $connection = $this->ab->getConnection();

        // Create experiments table.
        if ( ! Schema::connection($connection)->hasTable('experiments'))
        {
            Schema::connection($connection)->create('experiments', function($table)
            {
                $table->string('name');
                $table->integer('visitors')->unsigned()->default(0);
                $table->integer('engagement')->unsigned()->default(0);
            });
        }

        // Create goals table.
        if ( ! Schema::connection($connection)->hasTable('goals'))
        {
            Schema::connection($connection)->create('goals', function($table)
            {
                $table->string('name');
                $table->string('experiment');
                $table->integer('count')->unsigned()->default(0);
                $table->primary(array('name', 'experiment'));
            });
        }

        $this->info('Database schema initialized.');

        $experiments = $this->ab->getExperiments();

        if ( ! $experiments or empty($experiments))
        {
            return $this->error('No experiments configured.');
        }

        $goals = $this->ab->getGoals();

        if ( ! $goals or empty($goals))
        {
            $this->error('No goals configured.');
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
