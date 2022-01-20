<?php

namespace Tests\Unit;

use App\Models\Setting;
use Mockery;
use Symfony\Component\Console\Output\ConsoleOutput;
use Tests\TestCase;

class SettingModelTest extends TestCase
{
    /** @var \Mockery\MockInterface|Foo */
    private $mock;

    /** @var \Symfony\Component\Console\Output\ConsoleOutput */
    private $console;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mock = Mockery::mock(Setting::class);
        $this->console = new ConsoleOutput();
    }

    /** @test */
    public function mocking_get()
    {
        $settingValue = 'test_value';
        $this->mock->shouldReceive('get')->with('test_setting', $settingValue)->andReturn($settingValue);

        $this->app->instance(Setting::class, $this->mock);
        $setting = $this->app->getInstance()->make(Setting::class);
        $result = $setting::get('test_setting', $settingValue);
        // $this->console->writeln("<info>$result => $settingValue</info>");
        $this->assertEquals($settingValue, $result);
    }

    /** @test */
    public function mocking_set()
    {
        $settingValue = 'test_value';
        $this->mock->shouldReceive('set')->with('setting_key', $settingValue)->andReturn($settingValue);
        $this->app->instance(Setting::class, $this->mock);
        $setting = $this->app->getInstance()->make(Setting::class);
        $result = $setting::set('setting_key', $settingValue);
        // $this->console->writeln("<info>$result => $settingValue</info>");
        $this->assertEquals($settingValue, $result);
    }
}
