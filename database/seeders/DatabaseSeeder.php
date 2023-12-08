<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

// use App\Models\Package;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PackageSeeder::class,
            LevelSeeder::class,
            GallerySeeder::class,
            SemesterSeeder::class,
            ClassSeeder::class,
        ]);
    }
}
