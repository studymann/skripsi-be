<?php

namespace Database\Seeders;

use App\Models\Classes;
use App\Models\Level;
use App\Models\Package;
use App\Models\Semester;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $package = Package::where('name', 'A')->first();
        $level = Level::where('name', '1')->first();
        $semester = Semester::where('name', 'Genap')->first();

        $datas = [
            [
                'name' => 'Junior 1',
                'package_id' => $package->id,
                'level_id' => $level->id,
                'semester_id' => $semester->id,
                'year' => 2023,
            ],
            [
                'name' => 'Junior 2',
                'package_id' => $package->id,
                'level_id' => $level->id,
                'semester_id' => $semester->id,
                'year' => 2023,
            ],
            [
                'name' => 'Junior 3',
                'package_id' => $package->id,
                'level_id' => $level->id,
                'semester_id' => $semester->id,
                'year' => 2023,
            ],
            [
                'name' => 'Junior 4',
                'package_id' => $package->id,
                'level_id' => $level->id,
                'semester_id' => $semester->id,
                'year' => 2023,
            ],
            [
                'name' => 'Junior 5',
                'package_id' => $package->id,
                'level_id' => $level->id,
                'semester_id' => $semester->id,
                'year' => 2023,
            ],
        ];

        foreach ($datas as $data) {
            Classes::create($data);
        }
    }
}
