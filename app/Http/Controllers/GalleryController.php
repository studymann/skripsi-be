<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Helpers\EncodeFile;
use Illuminate\Http\Request;
use App\Helpers\PaginationHelper;
use App\Helpers\ResponseFormatter;
use App\Http\Requests\StoreGalleryRequest;
use App\Http\Requests\UpdateGalleryRequest;

class GalleryController extends Controller
{
    public function getList (Request $request) {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        if ($perPage === 'bypass' || $page === 'bypass') {
            // Jika per_page bernilai "bypass", gunakan metode bypass
            $galleries = Gallery::all();
            $total = $galleries->count();
            $data = $galleries->map(function ($gallery) {
                return [
                    'id' => $gallery->id,
                    'title' => $gallery->title,
                    'description' => $gallery->description,
                    'image' => EncodeFile::encodeFile(public_path('image/'.$gallery->image)),
                ];
            });
        } else {
            // Jika per_page memiliki nilai selain "bypass", gunakan paginasi
            $paginator = Gallery::paginate($perPage, ['*'], 'page', $page);
            $galleries = $paginator->items();
            $data = collect($galleries)->map(function ($gallery) {
                return [
                    'id' => $gallery->id,
                    'title' => $gallery->title,
                    'description' => $gallery->description,
                    'image' => EncodeFile::encodeFile(public_path('image/'.$gallery->image)),
                ];
            });
            $total = $paginator->total();
        }

        $nextPageUrl = $perPage === 'bypass' || $page === 'bypass' ? null : PaginationHelper::getNextPageUrl($request, $page, $perPage, $total);
        $prevPageUrl = $perPage === 'bypass' || $page === 'bypass' ? null : PaginationHelper::getPrevPageUrl($request, $page, $perPage);

        return ResponseFormatter::success([
            'current_page' => (int)$page,
            'data' => $data,
            'next_page_url' => $nextPageUrl,
            'path' => $request->url(),
            'per_page' => (int)$perPage,
            'prev_page_url' => $prevPageUrl,
            'to' => (int)$page * (int)$perPage,
            'total' => (int)$total,
        ], 'Berhasil Menampilkan Data Gallery');
    }
}
