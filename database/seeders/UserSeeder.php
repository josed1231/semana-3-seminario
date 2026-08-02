<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'josedavidzuluagabarco@gmail.com'], 
            [
                'name'     => 'Web Master',
                'username' => 'webmaster',
                'password' => Hash::make('password123'),
                'rol'      => 'admin',
            ]
        );
    }
}