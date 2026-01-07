<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetUsersCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-users-commands {url=https://jsonplaceholder.typicode.com/users} {limit=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = $this->argument('url');
        $limit = (int) $this->argument('limit');

        $response = Http::get($url);

        if (! $response->successful()) {
            $this->error("Request failed: {$response->status()}");

            return self::FAILURE;
        }

        $users = collect($response->json())->take($limit);

        foreach ($users as $user) {
            echo "Creating user: {$user['name']} ({$user['email']})\n";
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => \Hash::make('password'),
            ]);
            echo "Finished creating user: {$user['name']} ({$user['email']})\n";
        }
    }
}
