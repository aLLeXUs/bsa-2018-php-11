<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Repository\Contracts\UserRepository;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private $userRepository;

    protected function setUp()
    {
        parent::setUp();

        $this->userRepository = $this->app->make(UserRepository::class);
    }

    public function testCreate()
    {
        $user = new User([
            'name' => 'User1',
            'email' => 'user@example.com',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret,
        ]);
        $user->save();
        $this->assertNotNull($user->id);

        $this->assertEquals($user->toArray(), $this->userRepository->getById($user->id)->toArray());
    }
}
