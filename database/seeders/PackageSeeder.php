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
        // $packages = [];
        // for ($a = 1; $a <= 12; $a++ ) {
        //     if ($a <= 6 ) {
        //         $packages['name'] = "A";
        //     } else if ($a > 6 && $a <= 9) {
        //         $packages['name'] = "B";
        //     } else if ($a > 9 && $a <= 12) {
        //         $packages['name'] = "C";
        //     }
        //     Package::create($level);
        // }

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
