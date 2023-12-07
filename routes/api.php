<?php

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\GalleryController;

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
        'message' => 'Please log in first!!!'
    ], 401);
})->name('login');

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::middleware(['auth', 'jwt.verify', 'role:admin'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware(['auth', 'jwt.verify'])->get('/refresh-token', [AuthController::class, 'refreshToken']);

Route::get('gallery/get-list', [GalleryController::class, 'getList']);
Route::get('level/get-list', [LevelController::class, 'getList']);

Route::post('register', [AuthController::class,'regUser']);
Route::post('login', [AuthController::class,'logUser']);

Route::middleware(['auth', 'jwt.verify'])->group(function () {
    Route::get('get-token-info', [AuthController::class, 'getTokenInfo']);
    Route::get('refresh-token', [AuthController::class, 'refreshToken']);

    Route::apiResource('users', UserController::class);


    //gallery//
    Route::apiResource('gallery', GalleryController::class);
    //store
    Route::post('gallery', [GalleryController::class, 'store']);
});
