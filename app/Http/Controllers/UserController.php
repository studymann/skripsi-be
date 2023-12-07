<?php

namespace App\Http\Controllers;

use App\Models\User;
// use App\Helpers\EncodeFile;
use Illuminate\Http\Request;
use App\Helpers\PaginationHelper;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Hash;
use function PHPUnit\Framework\isEmpty;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
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
            $users = User::all();
            $total = $users->count();
            $data = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->roles->map(function ($role) {
                        return [
                            'id' => $role->id,
                            'name' => $role->name
                        ];
                    })
                ];
            });
        } else {
            // Jika per_page memiliki nilai selain "bypass", gunakan paginasi
            $paginator = User::paginate($perPage, ['*'], 'page', $page);
            $users = $paginator->items();
            $data = collect($users)->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->roles->map(function ($role) {
                        return [
                            'id' => $role->id,
                            'name' => $role->name
                        ];
                    })
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
        ], 'Berhasil Menampilkan Data User');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        try {
            $user = User::findOrFail($request->get('id'));
            if ($user) {
                $data = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->roles->map(function ($role) {
                        return [
                            'id' => $role->id,
                            'name' => $role->name
                        ];
                    }),
                ];

                return ResponseFormatter::success($data, 'Data User Berhasil');
            } else {
                return ResponseFormatter::error('', 'Data User Gagal');
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseFormatter::error('', 'Kesalahan Pada Sistem');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            "name" => "required",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:4",
            "role" => "required"
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed!!!',
                'data' =>  $validator->errors()
            ], 405);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        $roleId = $request->role;

        DB::table('role_user')->insert([
            'user_id' => $user->id,
            'role_id' => $roleId,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User has been created!!!'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = User::find($user->id);

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'User not found!!!',
            ], 404);
        }

        $rules = [
            "name" => "required",
            "email" => "required|email|unique:users,email," . $user->id,
            "password" => "nullable|min:4",
            "role" => "required"
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed!!!',
                'data' =>  $validator->errors()
            ], 405);
        }

        $data->name = $request->name;
        $data->email = $request->email;
        if ($request->filled('password')) {
            $data->password = Hash::make($request->password);
        }
        $data->save();

        $roleId = $request->role;

        DB::table('role_user')
            ->where('user_id', $data->id)
            ->delete();

            DB::table('role_user')->insert([
                'user_id' => $user->id,
                'role_id' => $roleId,
            ]);

        return response()->json([
            'status' => true,
            'message' => 'User has been updated!!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $data = User::find($user->id);
        DB::table('role_user')
        ->where('user_id', $data->id)
        ->delete();
        $data->delete();

        return response()->json([
            'status' => true,
            'message' => 'User has been deleted!!!'
        ], 200);
    }
}
