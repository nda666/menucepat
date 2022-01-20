<?php

namespace Tests\Unit;

use App\Models\Setting;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\Mock;
use Symfony\Component\Console\Output\ConsoleOutput;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    /** @var \Mockery\MockInterface|Foo */
    private $mock;

    /** @var \Mockery\MockInterface|Foo */
    private $repo;

    /** @var \Symfony\Component\Console\Output\ConsoleOutput */
    private $console;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mock = Mockery::mock(UserRepository::class);
        $this->console = new ConsoleOutput();
    }

    /** @test */
    public function mocking_reset_password()
    {
        $this->mock->shouldReceive('resetPassword')->withAnyArgs()->andReturn(new User());
        $this->app->instance(UserRepository::class, $this->mock);

        $userMock = $this->app->getInstance()->make(UserRepository::class);

        $requestParams = [
            'email'    => 'test@test.com'
        ];

        $request = $this->getMockBuilder('Illuminate\Http\Request')
            ->disableOriginalConstructor()
            ->onlyMethods(['get', 'post', 'all'])
            ->getMock();

        $request->expects($this->any())
            ->method('get')
            ->willReturn($requestParams);


        $col = $userMock->resetPassword($request);

        $this->assertInstanceOf(User::class, $col);
    }
}
