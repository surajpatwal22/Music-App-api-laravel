<?php

use App\Http\Controllers\SongsController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
Route::post('updateProfile', [UserController::class, 'updateProfile']);
Route::post('uploadSong',[SongsController::class,'upload']);
Route::get('getallsongs',[SongsController::class,'getAllSongs']);
   
});

Route::post('register', [UserController::class, 'register']);

Route::post('test',[TestController::class,"index"]);

