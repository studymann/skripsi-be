<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class EncodeFile
{
    public static function encodeFile($file)
    {
        // $base64Image = base64_encode(file_get_contents($file));
        // $mimeType = File::mimeType($file);
        // return 'data:' . $mimeType . ';base64,' . $base64Image;

        // Pastikan file ada
        if (file_exists($file)) {
            $base64Image = base64_encode(file_get_contents($file));
            $mimeType = File::mimeType($file);
            return 'data:' . $mimeType . ';base64,' . $base64Image;
        } else {
            // Handle jika file tidak ditemukan
            return 'File not found';
        }
    }
}
