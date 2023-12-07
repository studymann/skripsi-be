<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{


    public function regUser(Request $request)
    {
        $rules = [
            "name" => "required",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:4",
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ResponseFormatter::error('', $validator->errors());
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        $role = Role::where('name', 'Admin')->first();
        DB::table('role_user')->insert([
            'user_id' => $user->id,
            'role_id' => $role,
        ]);

        return ResponseFormatter::success($user, 'Registration has been successfully, please login!!!');
    }

    public function logUser(Request $request)
    {
        $rules = [
            "email" => "required|email",
            "password" => "required",
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ResponseFormatter::error('', $validator->errors());
        }

        if (!$token = Auth::attempt($request->only(['email', 'password']))) {
            return ResponseFormatter::error("Unauthorized", "Account isn't same...", 401);
        }

        return $this->createToken($token);
    }

    protected function createToken($token)
    {
        return ResponseFormatter::success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user' => Auth::user(),
        ], 'Login Success');
    }
}
