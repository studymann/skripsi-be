<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use App\Helpers\PaginationHelper;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
    public function getList(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        if ($perPage === 'bypass' || $page === 'bypass') {
            // Jika per_page bernilai "bypass", gunakan metode bypass
            $packages = Package::all();
            $total = $packages->count();
            $data = $packages->map(function ($package) {
                return [
                    'id' => $package->id,
                    'name' => $package->name,
                ];
            });
        } else {
            // Jika per_page memiliki nilai selain "bypass", gunakan paginasi
            $paginator = Package::paginate($perPage, ['*'], 'page', $page);
            $packages = $paginator->items();
            $data = collect($packages)->map(function ($package) {
                return [
                    'id' => $package->id,
                    'name' => $package->name,
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
        ], 'Berhasil Menampilkan Data package');
    }

    public function show(Request $request)
    {
        $package = Package::findOrFail($request->get('id'));

        if ($package) {
            $data = [
                'id' => $package->id,
                'name' => $package->name
            ];

            return ResponseFormatter::success($data, 'Data Berhasil');
        } else {
            return ResponseFormatter::error('', 'Data Gagal Dimuat');
        }
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ResponseFormatter::error('', $validator->errors());
        }

        try {
            DB::transaction(function () use ($request, &$packages) {
                $packages = Package::create([
                    'name' => $request->get('name'),
                ]);
            });
            if ($packages) {
                return ResponseFormatter::success($packages, 'Data Berhasil Disimpan');
            } else {
                return ResponseFormatter::error('', 'Data Gagal Disimpan');
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseFormatter::error('', 'Kesalahan Pada Sistem');
        }
    }

    public function edit(Request $request)
    {
        $package = Package::findOrFail($request->get('id'));

        if ($package) {
            return ResponseFormatter::success($package, 'Data Siap Diedit');
        } else {
            return ResponseFormatter::error('', 'Data Tidak Ditemukan');
        }
    }

    public function update(Request $request)
    {
        $rules = [
            'name' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ResponseFormatter::error('', $validator->errors());
        }

        try {
            DB::transaction(function () use ($request, &$packages) {
                $package = Package::findOrFail($request->get('id'));
                $packages = $package->update([
                    'name' => $request->get('name')
                ]);
            });
            if ($packages) {
                return ResponseFormatter::success($packages, 'Data Berhasil Terupdate');
            } else {
                return ResponseFormatter::error('', 'Data Gagal Disimpan');
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseFormatter::error('', 'Kesalahan Pada Sistem');
        }
    }

    public function destroy(Request $request)
    {
        $package = Package::findOrFail($request->get('id'));

        if ($package) {
            $package->delete();

            return ResponseFormatter::success('' , 'Data Berhasil Dihapus');
        } else {
            return ResponseFormatter::error('', 'Data Gagal Dihapus');
        }
    }
}
