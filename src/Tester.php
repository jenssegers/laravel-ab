<?php namespace Jenssegers\AB;

use Config;
use Session;
use URL;
use Route;

use Jenssegers\AB\Models\Experiment;
use Jenssegers\AB\Models\Goal;

class Tester {

    /**
     * Track clicked links and form submissions.
     *
     * @return void
     */
    public function track()
    {
        // Don't track if there is no active experiment.
        if ( ! Session::get('ab.experiment')) return;

        // Don't track first page view.
        if (is_null(URL::getRequest()->headers->get('referer'))) return;

        // Check current and previous urls.
        $root = URL::to('/');
        $from = ltrim(str_replace($root, '', URL::previous()), '/');
        $to = ltrim(str_replace($root, '', URL::current()), '/');

        // Don't track refreshes.
        if ($from == $to) return;

        // Trigger engagement.
        $this->interact();

        $goals = $this->getGoals();

        // Detect goal completion based on the current url.
        if (in_array($to, $goals) or in_array('/' . $to, $goals))
        {
            $this->complete($to);
        }

        // Detect goal completion based on the current route name.
        if ($route = Route::currentRouteName() and in_array($route, $goals))
        {
            $this->complete($route);
        }
    }

    /**
     * Increment the engagement for the current experiment.
     *
     * @return void
     */
    public function interact()
    {
        // Only interact once per experiment.
        if (Session::get('ab.interacted')) return;

        $experiment = Experiment::firstOrNew(['name' => $this->experiment()]);
        $experiment->engagement++;
        $experiment->save();

        // Mark current experiment as interacted.
        Session::set('ab.interacted', 1);
    }

    /**
     * Mark a goal as completed for the current experiment.
     *
     * @return void
     */
    public function complete($name)
    {
        // Only complete once per experiment.
        if (Session::get("ab.completed_$name")) return;

        $goal = Goal::firstOrNew(['name' => $name, 'experiment' => $this->experiment()]);
        $goal->count++;
        $goal->save();

        // Mark current experiment as completed.
        Session::set("ab.completed_$name", 1);
    }

    /**
     * Get or compare the current experiment for this session.
     *
     * @param  string  $target
     * @return bool|string
     */
    public function experiment($target = null)
    {
        // Get the existing or new experiment.
        $experiment = Session::get('ab.experiment') ?: $this->nextExperiment();

        if (is_null($target))
        {
            return $experiment;
        }

        return $experiment == $target;
    }

    /**
     * Alias for experiment.
     *
     * @param  string  $target
     * @return bool|string
     */
    public function getExperiment($target = null)
    {
        return $this->experiment($target);
    }

    /**
     * Set the current experiment for this session manually.
     *
     * @param string $experiment
     */
    public function setExperiment($experiment)
    {
        if (Session::get('ab.experiment') != $experiment)
        {
            Session::set('ab.experiment', $experiment);

            // Increase pageviews for new experiment.
            $this->nextExperiment($experiment);
        }
    }

    /**
     * Get all experiments.
     *
     * @return array
     */
    public function getExperiments()
    {
        return Config::get('ab::experiments', []);
    }

    /**
     * Get all goals.
     *
     * @return array
     */
    public function getGoals()
    {
        return Config::get('ab::goals', []);
    }

    /**
     * Prepare an experiment for this session.
     *
     * @return string
     */
    protected function nextExperiment($experiment = null)
    {
        // Verify that the experiments are in the database.
        $this->checkExperiments();

        if ($experiment)
        {
            $experiment = Experiment::active()->findOrfail($experiment);
        }
        else
        {
            $experiment = Experiment::active()->orderBy('visitors', 'asc')->firstOrFail();
        }

        // Increase the visitors counter.
        $experiment->visitors++;
        $experiment->save();

        Session::set('ab.experiment', $experiment->name);

        return $experiment->name;
    }

    /**
     * Add experiments to the database.
     *
     * @return void
     */
    protected function checkExperiments()
    {
        // Check if the database contains all experiments.
        if (Experiment::active()->count() != count($this->getExperiments()))
        {
            // Insert all experiments.
            foreach ($this->getExperiments() as $experiment)
            {
                Experiment::firstOrCreate(['name' => $experiment]);
            }
        }
    }

}
