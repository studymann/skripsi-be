<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Helpers\EncodeFile;
use Illuminate\Http\Request;
use App\Helpers\PaginationHelper;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreGalleryRequest;
use App\Http\Requests\UpdateGalleryRequest;

class GalleryController extends Controller
{
    public function getList(Request $request) {
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

    public function show(Request $request)
    {
        $id = $request->get('id');
        $gallery = Gallery::findOrFail($id);

        if (!$gallery) {
            return ResponseFormatter::error('', 'Data Tidak Ditemukan');
        }

        $data = [
            'id' => $gallery->id,
            'title' => $gallery->title,
            'description' => $gallery->description,
            'image' => EncodeFile::encodeFile(public_path('image/'.$gallery->image))
        ];

        return ResponseFormatter::success($data, 'GetShow Gallery Berhasil!');
    }

    public function create()
    {
        return ResponseFormatter::success('', 'Halaman Create Gallery');
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ResponseFormatter::error('', $validator->errors());
        }

        try {
            DB::transaction(function () use ($request, &$galleries) {
                $tittle = $request->get('tittle');
                $description = $request->get('description');
                $file = $request->file('image');

                // Manipulasi gambar menggunakan Intervention Image
                $image = Image::make($file);
                $image->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                // $imageData = $file->getClientOriginalExtension();
                // Extract the filename without the extension
                $imageData = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $imageFileName = strtotime(date('Y-m-d H:i:s')) . '.' . $imageData . '.webp';

                // Simpan file dengan nama yang sudah dikodekan ke direktori yang sesuai
                $image->save(public_path() . '/' . env('UPLOADS_DIRECTORY') . '/' . $imageFileName, 90, 'webp');

                $galleries = Gallery::create([
                    'tittle' => $tittle,
                    'description' => $description,
                    'image' => $imageFileName,
                ]);
            });
            if ($galleries) {
                return ResponseFormatter::success($galleries, 'Data Berhasil Disimpan');
            } else {
                return ResponseFormatter::error('', 'Data Gagal Disimpan');
            }
        } catch (\Exception $e) {
            return ResponseFormatter::error('', 'Terjadi Kesalahan Sistem');
        }
    }

    public function edit()
    {
        return ResponseFormatter::success('', 'Halaman Edit');
    }

    public function update(Request $request)
    {
        //rules
        $rules = [
            'tittle' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Melakukan validasi
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ResponseFormatter::error($validator->errors(), 'Validasi Gagal');
        }

        //getID
        $id = $request->get('id');

        try {
            DB::transaction(function () use ($request, $id, &$galleries) {

                $gallery = Gallery::where('id', $id)->first();

                if (!$gallery) {
                    return ResponseFormatter::error('', 'Data tidak ditemukan');
                }

                $delete = File::delete(public_path('image/' . $gallery->image));

                if(!$delete) {
                    return ResponseFormatter::error('', 'Image Tidak Terhapus');
                }

                $tittle = $request->get('tittle');
                $description = $request->get('description');
                $file = $request->file('image');
                // Manipulasi gambar menggunakan Intervention Image
                $image = Image::make($file);
                $image->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                // Extract the filename without the extension
                $imageData = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $imageFileName = strtotime(date('Y-m-d H:i:s')) . '.' . $imageData . '.webp';
                //simpan
                $image->save(public_path() . '/' . env('UPLOADS_DIRECTORY') . '/' . $imageFileName, 90, 'webp');

                $galleries = $gallery->update([
                    'tittle' => $tittle,
                    'description' => $description,
                    // 'category_id' => $category_id,
                    'image' => $imageFileName
                ]);


            });
            if ($galleries) {
                return ResponseFormatter::success($galleries, 'Data Berhasil Disimpan');
            } else {
                return ResponseFormatter::error('', 'Data Gagal Disimpan');
            }
        } catch (\Exception $e) {
            return ResponseFormatter::error('', 'Terjadi Kesalahan Sistem');
        }
    }

    public function destroy(Request $request)
    {
        $id = $request->get('id');
        $gallery = Gallery::findOrFail($id);

        if ($gallery) {
            //hapus image
            $fileToDelete = public_path('image/' . $gallery->image);
            if (file_exists($fileToDelete)) {
                unlink($fileToDelete);
            }

            $gallery->delete();

            return ResponseFormatter::success('', 'Data Berhasil Dihapus');
        } else {
            return ResponseFormatter::error('', 'Data Gagal Dihapus');
        }
    }


}
