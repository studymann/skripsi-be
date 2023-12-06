<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = User::all([
            'id',
            'name',
            'email',
        ]);
        return response()->json([
            'status' => true,
            'message' => 'All data users',
            'payload' => $data
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        try {
            $data = User::findOrFail($user->id);

            return response()->json([
                'status' => true,
                'message' => 'Detail data user',
                'payload' => $data
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'error' => 'User not found!!!',
                'message' => $e->getMessage()
            ], 404);
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
