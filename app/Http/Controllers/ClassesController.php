<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\Classes;
use App\Models\Package;
use App\Models\Semester;
use App\Helpers\EncodeFile;
use Illuminate\Http\Request;
use App\Helpers\PaginationHelper;
use App\Helpers\ResponseFormatter;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClassesController extends Controller
{
    public function getList(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        if ($perPage === 'bypass' || $page === 'bypass') {
            // Jika per_page bernilai "bypass", gunakan metode bypass
            $classes = Classes::all();
            $total = $classes->count();
            $data = $classes->map(function ($class) {
                return [
                    'name' => $class->name,
                    'package' => $class->package->name,
                    'level' => $class->level->name,
                    'semester' => $class->semester->name,
                    'year' => $class->year
                ];
            });
        } else {
            // Jika per_page memiliki nilai selain "bypass", gunakan paginasi
            $paginator = Classes::paginate($perPage, ['*'], 'page', $page);
            $classes = $paginator->items();
            $data = collect($classes)->map(function ($class) {
                return [
                    'name' => $class->name,
                    'package' => $class->package->name,
                    'level' => $class->level->name,
                    'semester' => $class->semester->name,
                    'year' => $class->year
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
        ], 'Berhasil Menampilkan Data class');
    }

    public function show(Request $request)
    {
        $class = Classes::findOrFail($request->get('id'));

        if ($class) {
            $data = [
                'id' => $class->id,
                'package' => $class->package->name,
                'level' => $class->level->name,
                'semester' => $class->semester->name,
                'year' => $class->year
            ];

            return ResponseFormatter::success($data, 'Data Berhasil Dimuat');
        } else {
            return ResponseFormatter::error('', 'Data Tidak Ditemukan');
        }
    }

    public function create()
    {
        $packages = Package::all('id', 'name');
        $levels = Level::all('id', 'name');
        $semesters = Semester::all('id', 'name');

        if ($packages && $levels && $semesters) {
            return ResponseFormatter::success([
                'packages' => $packages,
                'levels' => $levels,
                'semesters' => $semesters
            ], 'Form Create Class');
        } else {
            return ResponseFormatter::error('', 'Ada Kesalahan');
        }
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'package_id' => 'required',
            'level_id' => 'required',
            'semester_id' => 'required',
            'year' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            return ResponseFormatter::error('', $validator->errors());
        }

        try {
            DB::transaction(function () use ($request, &$classes) {
                $classes = Classes::create([
                    'name' => $request->get('name'),
                    'package_id' => $request->get('package_id'),
                    'level_id' => $request->get('level_id'),
                    'semester_id' => $request->get('semester_id'),
                    'year' => $request->get('year'),
                ]);
            });

            // DB::table('class_users')
            // ->where('student_id', $classes->)
            // ->delete();

            // DB::table('role_user')->insert([
            //     'user_id' => $user->id,
            //     'role_id' => $roleId,
            // ]);
            if ($classes) {
                return ResponseFormatter::success($classes, 'Data Berhasil Disimpan');
            } else {
                return ResponseFormatter::error('', 'Data Gagal Disimpan');
            }
        } catch (\Exception $e) {
            return ResponseFormatter::error('', 'Terjadi Kesalahan Sistem');
        }
    }

    public function edit(Request $request)
    {
        $class = Classes::findOrFail($request->get('id'));
        $packages = Package::all('id', 'name');
        $levels = Level::all('id', 'name');
        $semesters = Semester::all('id', 'name');

        if ($packages && $levels && $semesters) {
            return ResponseFormatter::success([
                'class' => [
                    'id' => $class->id,
                    'package' => $class->package->name,
                    'level' => $class->level->name,
                    'semester' => $class->semester->name,
                    'year' => $class->year
                ],
                'packages' => $packages,
                'levels' => $levels,
                'semesters' => $semesters
            ], 'Form Edit Class');
        } else {
            return ResponseFormatter::error('', 'Ada Kesalahan');
        }
    }

    public function update(Request $request)
    {
        $rules = [
            'name' => 'required',
            'package_id' => 'required',
            'level_id' => 'required',
            'semester_id' => 'required',
            'year' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            return ResponseFormatter::error('', $validator->errors());
        }

        try {
            DB::transaction(function () use ($request, &$classes) {
                $class = Classes::findOrFail($request->get('id'));
                $classes = $class->update([
                    'name' => $request->get('name'),
                    'package_id' => $request->get('package_id'),
                    'level_id' => $request->get('level_id'),
                    'semester_id' => $request->get('semester_id'),
                    'year' => $request->get('year'),
                ]);
            });
            if ($classes) {
                return ResponseFormatter::success($classes, 'Data Berhasil Diupdate');
            } else {
                return ResponseFormatter::error('', 'Data Gagal Diupdate');
            }
        } catch (\Exception $e) {
            return ResponseFormatter::error('', 'Terjadi Kesalahan Sistem');
        }
    }

    public function destroy(Request $request)
    {
        $class = Classes::findOrFail($request->get('id'));

        if($class) {
            $class->delete();

            return ResponseFormatter::success('', 'Data Berhasil Dihapus');
        } else {
            return ResponseFormatter::error('', 'Data Gagal Dihapus');
        }
    }
}
