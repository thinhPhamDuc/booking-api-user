<?php

namespace App\Console\Commands;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user['name'] = $this->ask('Name of the new user');
        $user['email'] = $this->ask('Email of the new user');
        $user['password'] = Hash::make($this->secret('Password of the new user'));
        $roleName = $this->choice('Password of the new user',['admin','editor']);
        $role = Roles::where('name',$roleName)->first();

        if (!$role) {
            $this->error('Role not found');
            return 1;
        }
        DB::transaction(function () use ($user, $role){
            $newUser = User::create($user);
            $newUser->roles()->attach($role->id);
        });
        
        $this->info('User '.$user['name'].' created successfully');
        return 0;
    }
}
