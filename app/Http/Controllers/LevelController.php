<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Helpers\EncodeFile;
use Illuminate\Http\Request;
use App\Helpers\PaginationHelper;
use App\Helpers\ResponseFormatter;
use App\Models\Package;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class LevelController extends Controller
{
    public function getList(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        if ($perPage === 'bypass' || $page === 'bypass') {
            // Jika per_page bernilai "bypass", gunakan metode bypass
            $levels = Level::all();
            $total = $levels->count();
            $data = $levels->map(function ($level) {
                return [
                    'id' => $level->id,
                    'package' => $level->package->name,
                    'name' => $level->name,
                ];
            })->sortBy('name')->values();
        } else {
            // Jika per_page memiliki nilai selain "bypass", gunakan paginasi
            $paginator = Level::paginate($perPage, ['*'], 'page', $page);
            $levels = $paginator->items();
            $data = collect($levels)->map(function ($level) {
                return [
                    'id' => $level->id,
                    'package' => $level->package->name,
                    'name' => $level->name,
                ];
            })->sortBy('name')->values();
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
        ], 'Berhasil Menampilkan Data level');
    }

    public function show(Request $request)
    {
        $id = $request->get('id');
        $level = Level::findOrfail($id);

        $data = [
            'id' => $level->id,
            'package' => $level->package->name,
            'name' => $level->name,
        ];

        if (!$level) {
            return ResponseFormatter::error('', 'Data Tidak Ditemukan');
        }

        return ResponseFormatter::success($data, 'Data Ditemukan');
    }

    public function create()
    {
        $packages = Package::select('id', 'name')->get();
        if (!$packages) {
            return ResponseFormatter::error('', 'Data Packages Error');
        }
        return ResponseFormatter::success(['packages' => $packages], 'Halaman Create Level');
    }

    public function store(Request $request)
    {
        $rules = [
            'package_id' => 'required',
            'name' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ResponseFormatter::error('', $validator->errors());
        }

        try {
            DB::transaction(function () use ($request, &$levels) {
                $package_id = $request->get('package_id');
                $name = $request->get('name');

                $levels = Level::create([
                    'package_id' => $package_id,
                    'name' => $name
                ]);
            });
            if ($levels) {
                return ResponseFormatter::success($levels, 'Data Berhasil Disimpan');
            } else {
                return ResponseFormatter::error('', 'Data Gagal Disimpan');
            }
        } catch (\Exception $e) {
            return ResponseFormatter::error('', 'Terjadi Kesalahan Sistem');
        }
    }

    public function edit(Request $request)
    {
        $packages = Package::select('id', 'name')->get();
        if (!$packages) {
            return ResponseFormatter::error('', 'Data Packages Error');
        }

        $id = $request->get('id');
        $level = Level::finOrFail($id);

        $datas = [
            'id' => $level->id,
            'name' => $level->name,
            'package' => $level->package->name,
            'packages' => $packages,
        ];

        if (!$level) {
            return ResponseFormatter::error('', 'Data Tidak Ditemukan');
        }

        return ResponseFormatter::success($datas, 'Data Berhasil Ditemukan');
    }

    public function update(Request $request)
    {
        $rules = [
            'package_id' => 'required',
            'name' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ResponseFormatter::error('', $validator->errors());
        }

        $id = $request->get('id');
        try {
            DB::transaction(function () use ($request, $id, &$levels) {
                $level = Level::findOrFail($id);

                if (!$level) {
                    return ResponseFormatter::error('', 'Data Tidak Ditemukan');
                }

                $name = $request->get('name');
                $package_id = $request->get('package_id');

                $levels = $level->update([
                    'name' => $name,
                    'package_id' => $package_id
                ]);
            });
            if ($levels) {
                return ResponseFormatter::success($levels, 'Data Berhasil Diubah');
            } else {
                return ResponseFormatter::error('', 'Data Gagal Diubah');
            }
        } catch (\Exception $e) {
            return ResponseFormatter::error('', 'Terjadi Kesalahan Sistem');
        }
    }

    public function destroy(Request $request)
    {
        $id = $request->get('id');
        $level = Level::finOrFail($id);

        if ($level) {
            $level->delete();

            return ResponseFormatter::success('', 'Data Berhasil Dihapus');
        } else {
            return ResponseFormatter::error('', 'Data Gagal Dihapus');
        }
    }
}
