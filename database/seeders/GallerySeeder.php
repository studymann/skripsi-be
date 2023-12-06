<?php

namespace Database\Seeders;

// use Carbon\Factory;

use App\Models\Gallery;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $galleries = [
            ['title' => 'lorem1', 'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Vel, officia quod, voluptatum quos labore molestiae ipsa dolorum natus, error in repellendus! Maiores architecto sit quisquam at! Iste nam ullam dolor.', 'image' => 'Sulthan.png'],
            ['title' => 'lorem2', 'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Vel, officia quod, voluptatum quos labore molestiae ipsa dolorum natus, error in repellendus! Maiores architecto sit quisquam at! Iste nam ullam dolor.', 'image' => 'Sulthan.png'],
            ['title' => 'lorem3', 'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Vel, officia quod, voluptatum quos labore molestiae ipsa dolorum natus, error in repellendus! Maiores architecto sit quisquam at! Iste nam ullam dolor.', 'image' => 'Sulthan.png'],
            ['title' => 'lorem4', 'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Vel, officia quod, voluptatum quos labore molestiae ipsa dolorum natus, error in repellendus! Maiores architecto sit quisquam at! Iste nam ullam dolor.', 'image' => 'Sulthan.png'],
            ['title' => 'lorem5', 'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Vel, officia quod, voluptatum quos labore molestiae ipsa dolorum natus, error in repellendus! Maiores architecto sit quisquam at! Iste nam ullam dolor.', 'image' => 'Sulthan.png'],
        ];

        foreach ($galleries as $gallery) {
            Gallery::create($gallery);
        }
    }


}
