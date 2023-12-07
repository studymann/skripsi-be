<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;
use App\Helpers\PaginationHelper;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreSemesterRequest;
use App\Http\Requests\UpdateSemesterRequest;

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getList(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        if ($perPage === 'bypass' || $page === 'bypass') {
            // Jika per_page bernilai "bypass", gunakan metode bypass
            $semesters = Semester::all();
            $total = $semesters->count();
            $data = $semesters->map(function ($semester) {
                return [
                    'id' => $semester->id,
                    'name' => $semester->name,
                ];
            });
        } else {
            // Jika per_page memiliki nilai selain "bypass", gunakan paginasi
            $paginator = Semester::paginate($perPage, ['*'], 'page', $page);
            $semesters = $paginator->items();
            $data = collect($semesters)->map(function ($semester) {
                return [
                    'id' => $semester->id,
                    'name' => $semester->name,
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
        ], 'Berhasil Menampilkan Data semester');
    }

    public function show(Request $request)
    {
        $semester = Semester::findOrFail($request->get('id'));

        if ($semester) {
            $data = [
                'id' => $semester->id,
                'name' => $semester->name
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
            DB::transaction(function () use ($request, &$semesters) {
                $semesters = Semester::create([
                    'name' => $request->get('name'),
                ]);
            });
            if ($semesters) {
                return ResponseFormatter::success($semesters, 'Data Berhasil Disimpan');
            } else {
                return ResponseFormatter::error('', 'Data Gagal Disimpan');
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseFormatter::error('', 'Kesalahan Pada Sistem');
        }
    }

    public function edit(Request $request)
    {
        $semester = Semester::findOrFail($request->get('id'));

        if ($semester) {
            return ResponseFormatter::success($semester, 'Data Siap Diedit');
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
            DB::transaction(function () use ($request, &$semesters) {
                $semester = Semester::findOrFail($request->get('id'));
                $semesters = $semester->update([
                    'name' => $request->get('name')
                ]);
            });
            if ($semesters) {
                return ResponseFormatter::success($semesters, 'Data Berhasil Terupdate');
            } else {
                return ResponseFormatter::error('', 'Data Gagal Disimpan');
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseFormatter::error('', 'Kesalahan Pada Sistem');
        }
    }

    public function destroy(Request $request)
    {
        $semester = Semester::findOrFail($request->get('id'));

        if ($semester) {
            $semester->delete();

            return ResponseFormatter::success('' , 'Data Berhasil Dihapus');
        } else {
            return ResponseFormatter::error('', 'Data Gagal Dihapus');
        }
    }
}
