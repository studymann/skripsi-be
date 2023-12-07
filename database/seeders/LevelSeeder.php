<?php

namespace Database\Seeders;

use App\Models\Level;
use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $level = [];
        for ($a = 1; $a <= 12; $a++ ) {
            // $level['name'] = $a;
            if ($a <= 6 ) {
                $package = Package::where('name', 'A')->first();
                $level['package_id'] = $package->id;
            } else if ($a > 6 && $a <= 9) {
                $package = Package::where('name', 'B')->first();
                $level['package_id'] = $package->id;
            } else if ($a > 9 && $a <= 12) {
                $package = Package::where('name', 'C')->first();
                $level['package_id'] = $package->id;
            }
            $level['name'] = $a;
            Level::create($level);
        }
    }


}
