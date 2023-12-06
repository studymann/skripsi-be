<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // protected function createToken($token)
    // {
    //     return response()->json([
    //         'access_token' => $token,
    //         'token_type' => 'bearer',
    //         'expires_in' => auth()->factory()->getTTL() * 30,
    //         'user' => auth()->user()
    //     ]);
    // }

    // public function refreshToken()
    // {
    //     $newToken = auth()->refresh();
    //     return $this->createToken($newToken);
    // }

    public function regUse(Request $request)
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

        DB::table('role_user')->insert([
            'user_id' => $user->id,
            'role_id' => 1,
        ]);

        return ResponseFormatter::success($user, 'Registration has been successfully, please login!!!');
    }

    public function logUse(Request $request)
    {
        $rules = [
            "email" => "required",
            "password" => "required",
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ResponseFormatter::error('', $validator->errors());
        }

        if (!$token = Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'status' => false,
                'message' => "Account isn't same..."
            ], 401);
        }

        return $this->createToken($token);
    }
}
