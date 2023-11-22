<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected function createToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 30,
            'user' => auth()->user()
        ]);
    }

    public function refreshToken()
    {
        $token = auth()->refresh();
        return $this->createToken($token);
    }

    public function regUse(Request $request)
    {
        $rules = [
            "name" => "required",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:4",
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

        DB::table('role_user')->insert([
            'user_id' => $user->id,
            'role_id' => 1,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Registration has been successfully, please login!!!'
        ], 200);
    }

    public function logUse(Request $request)
    {
        $rules = [
            "email" => "required",
            "password" => "required",
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed!!!',
                'data' =>  $validator->errors()
            ], 405);
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
