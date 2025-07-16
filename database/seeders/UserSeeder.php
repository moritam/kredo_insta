<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::create([
            'name' => 'test-a',
            'email' => 'test-a@gmail.com',
            'password' => Hash::make('test12345'),
            'role_id' => 2
        ]);

        User::create([
            'name' => 'test-b',
            'email' => 'test-b@gmail.com',
            'password' => Hash::make('test12345'),
            'role_id' => 2
        ]);

        User::create([
            'name' => 'test-c',
            'email' => 'test-c@gmail.com',
            'password' => Hash::make('test12345'),
            'role_id' => 2
        ]);

        User::create([
            'name' => 'test-d',
            'email' => 'test-d@gmail.com',
            'password' => Hash::make('test12345'),
            'role_id' => 2
        ]);

        User::create([
            'name' => 'test-e',
            'email' => 'test-e@gmail.com',
            'password' => Hash::make('test12345'),
            'role_id' => 2
        ]);

        User::create([
            'name' => 'Hayato',
            'email' => 'hayato.moritan@gmail.com',
            'password' => Hash::make('test12345'),
            'role_id' => 2
        ]);

        User::create([
            'name' => 'Jiro',
            'email' => 'Jiro@gmail.com',
            'password' => Hash::make('test12345'),
            'role_id' => 2
        ]);
    }
}
