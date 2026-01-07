<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GetUsersCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_users_from_api_with_limit()
    {
        Http::fake([
            'jsonplaceholder.typicode.com/users' => Http::response([
                ['name' => 'A', 'email' => 'a@test.com'],
                ['name' => 'B', 'email' => 'b@test.com'],
                ['name' => 'C', 'email' => 'c@test.com'],
            ], 200),
        ]);

        $exitCode = Artisan::call('app:get-users-commands', [
            'url' => 'https://jsonplaceholder.typicode.com/users',
            'limit' => 2,
        ]);

        $this->assertSame(0, $exitCode);

        $this->assertDatabaseHas('users', ['email' => 'a@test.com']);
        $this->assertDatabaseHas('users', ['email' => 'b@test.com']);
        $this->assertDatabaseMissing('users', ['email' => 'c@test.com']);
    }

    public function test_it_fails_if_api_returns_error()
    {
        Http::fake([
            '*' => Http::response([], 500),
        ]);

        $exitCode = Artisan::call('app:get-users-commands', [
            'url' => 'https://jsonplaceholder.typicode.com/users',
            'limit' => 2,
        ]);

        $this->assertSame(1, $exitCode);
        $this->assertDatabaseCount('users', 0);
    }
}
