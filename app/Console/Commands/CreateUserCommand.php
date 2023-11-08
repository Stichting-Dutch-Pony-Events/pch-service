<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create User';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Create User');
        $name = $this->ask('What is the name of the new user.');
        $username = $this->ask('What is the username of the new user.');
        $password = $this->secret('What is the password of the new user.');

        $user = User::create([
            'name'     => $name,
            'username' => $username,
            'password' => Hash::make($password),
        ]);

        $this->info('User created successfully.');
    }
}
