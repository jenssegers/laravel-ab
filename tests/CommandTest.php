<?php
require_once 'TestCase.php';

use Jenssegers\AB\Tester;
use Jenssegers\AB\Models\Experiment;
use Jenssegers\AB\Models\Goal;
use Jenssegers\AB\Commands\InstallCommand;

class CommandTest extends TestCase {

    public function testInstall()
    {
        Artisan::call('ab:install');

        $this->assertTrue(Schema::hasTable('experiments'));
        $this->assertTrue(Schema::hasTable('goals'));
    }

    public function testFlush()
    {
        Artisan::call('ab:install');

        Experiment::create(['name'=>'z', 'visitors'=>1, 'engagement'=>1]);
        Goal::create(['name'=>'foo', 'experiment'=>'z', 'count'=>1]);

        Artisan::call('ab:flush');

        $this->assertEquals(3, Experiment::count());
        $this->assertEquals(0, Goal::count());
    }

    public function testReport()
    {
        Artisan::call('ab:install');

        Experiment::find('a')->update(['visitors' => 153, 'engagement' => 35]);
        Goal::create(['name'=>'foo', 'experiment'=>'a', 'count'=>42]);

        $output = new Symfony\Component\Console\Output\BufferedOutput;
        Artisan::call('ab:report', [], $output);
        $report = $output->fetch();

        $this->assertContains('Foo', $report);
        $this->assertContains('153', $report);
        $this->assertContains('35', $report);
        $this->assertContains('42', $report);
    }

}
