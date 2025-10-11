<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run():void
    {
        User::create([
            'name' => 'Admin.1',
            'email' => 'Admin.2@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]);
    }
}
