<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
            $role = Role::where('name', 'admin')->first();
            $user = User::create($item);
            DB::table('role_user')->insert([
                'user_id' => $user->id,
                'role_id' => $role->id,
            ]);
        }
    }
}
