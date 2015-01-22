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

        Experiment::find('a')->update(['visitors' => 153, 'engagement' => 35]);

        Artisan::call('ab:flush');

        $experiment = Experiment::find('a');

        $this->assertEquals(0, $experiment->visitors);
        $this->assertEquals(0, $experiment->engagement);
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

    public function testExport()
    {
        Artisan::call('ab:install');

        Experiment::find('a')->update(['visitors' => 153, 'engagement' => 35]);
        Goal::create(['name'=>'foo', 'experiment'=>'a', 'count'=>42]);

        $output = new Symfony\Component\Console\Output\BufferedOutput;
        Artisan::call('ab:export', [], $output);
        $report = $output->fetch();

        $this->assertContains('Foo', $report);
        $this->assertContains('153', $report);
        $this->assertContains('35', $report);
        $this->assertContains('42', $report);

        $output = new Symfony\Component\Console\Output\BufferedOutput;
        Artisan::call('ab:export', ['file' => '/tmp/test.csv'], $output);
        $report = $output->fetch();

        $this->assertContains('Creating /tmp/test.csv', $report);
    }

}
