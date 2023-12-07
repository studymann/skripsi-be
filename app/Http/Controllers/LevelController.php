<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Helpers\EncodeFile;
use Illuminate\Http\Request;
use App\Helpers\PaginationHelper;
use App\Helpers\ResponseFormatter;

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
}
