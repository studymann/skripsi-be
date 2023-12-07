<?php

namespace Database\Seeders;

use App\Models\Level;
use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = [
            ['name' => 'A'],
            ['name' => 'B'],
            ['name' => 'C'],
        ];

        foreach ($datas as $data) {
            Package::create($data);
        }
    }
}
