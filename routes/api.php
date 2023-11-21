<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/', function() {
    return response()->json([
        'status' => false,
        'message' => 'Access not allowed!!!'
    ], 401);
})->name('login');

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::middleware(['auth', 'jwt.verify', 'role:admin'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware(['auth', 'jwt.verify'])->get('/refresh-token', [AuthController::class, 'refreshToken']);

Route::post('register', [AuthController::class,'regUse']);
Route::post('login', [AuthController::class,'logUse']);
