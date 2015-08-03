<?php namespace Jenssegers\AB\Commands;

use Jenssegers\AB\Models\Experiment;
use Jenssegers\AB\Models\Goal;

use SplTempFileObject;
use League\Csv\Writer;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ExportCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ab:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export the A/B testing repor to a CSV file.';

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
        $experiments = Experiment::active()->get();
        $goals = array_unique(Goal::active()->orderBy('name')->lists('name'));

        $columns = array_merge(['Experiment', 'Visitors', 'Engagement'], array_map('ucfirst', $goals));

        $writer = new Writer(new SplTempFileObject);
        $writer->insertOne($columns);

        foreach ($experiments as $experiment)
        {
            $engagement = $experiment->visitors ? ($experiment->engagement / $experiment->visitors * 100) : 0;

            $row = [
                $experiment->name,
                $experiment->visitors,
                number_format($engagement, 2) . " % (" . $experiment->engagement .")",
            ];

            $results = $experiment->goals()->lists('count', 'name');

            foreach ($goals as $column)
            {
                $count = array_get($results, $column, 0);
                $percentage = $experiment->visitors ? ($count / $experiment->visitors * 100) : 0;

                $row[] = number_format($percentage, 2) . " % ($count)";
            }

            $writer->insertOne($row);
        }

        $output = (string) $writer;

        if ($file = $this->argument('file'))
        {
            $this->info("Creating $file");

            File::put($file, $output);
        }
        else
        {
            $this->line($output);
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('file', InputArgument::OPTIONAL, 'The target CSV file to write the output to.')
        );
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
