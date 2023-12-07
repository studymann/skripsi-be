<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Sulthan', 'email' => 'sulthan@gmail.com', 'password' => Hash::make('password')],
            ['name' => 'Hilman', 'email' => 'hilman@gmail.com', 'password' => Hash::make('password')],
        ];

        foreach ($data as $item) {
            User::create($item);
        }
    }
}
