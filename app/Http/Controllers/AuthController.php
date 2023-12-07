<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
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

        $role = Role::where('name', 'admin')->first();
        DB::table('role_user')->insert([
            'user_id' => $user->id,
            'role_id' => $role->id,
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

    public function logout(Request $request)
    {
        try {
            $token = JWTAuth::getToken(); // Mendapatkan token dari permintaan
            JWTAuth::invalidate($token); // Mematikan token

            return ResponseFormatter::success('', 'Logout Berhasil');
        } catch (\Exception $exception) {
            return ResponseFormatter::error('', 'Terjadi Kesalahan sistem', 500);
        }
    }

    public function getTokenInfo()
    {
        // Dapatkan user yang di-authenticated saat ini
        $user = Auth::user();

        // Buat token untuk user
        $token = JWTAuth::fromUser($user);

       return ResponseFormatter::success([
           'access_token' => $token,
           'token_type' => 'bearer',
           'expires_in' => JWTAuth::factory()->getTTL() * 60,
           'user' => $user
       ], 'Token Berhasil Diperoleh!');
    }

    public function refreshToken()
    {
        try {
            // Coba refresh token
            $token = JWTAuth::refresh(JWTAuth::getToken());

            // Dapatkan user terkini setelah refresh token
            $user = Auth::user();

            return ResponseFormatter::success([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
                'user' => $user,
            ], 'Token Berhasil Diperbaharui!');
        } catch (\Exception $e) {
            // Tangkap dan tangani eksepsi jika ada kesalahan dalam menyegarkan token
            return ResponseFormatter::error(null, 'Gagal menyegarkan token', 401);
        }
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
