<?php

use App\Http\Controllers\SongsController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GenreController;

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
Route::post('/user/songs', [UserSongController::class, 'SongListen']);
Route::get('/user_songs', [UserSongController::class, 'getUserSong']);
   
});

Route::post('register', [UserController::class, 'register']);

Route::post('test',[TestController::class,"index"]);
Route::post('search', [SongsController::class, 'search']);

Route::post('uploadSong',[SongsController::class,'upload']);
Route::get('getallsongs',[SongsController::class,'getAllSongs']);
//  album
Route::post('albums', [AlbumController::class, 'store']);
Route::put('albums/{id}', [AlbumController::class, 'update']);
Route::delete('albums/{id}', [AlbumController::class, 'destroy']);
Route::get('albums', [AlbumController::class, 'getAllAlbum']);
Route::get('albums/{id}', [AlbumController::class, 'getAlbum']);

//  genre
Route::post('genres', [GenreController::class, 'store']);
Route::put('genres/{id}', [GenreController::class, 'update']);
Route::delete('genres/{id}', [GenreController::class, 'destroy']);

// language

Route::post('language', [LanguageController::class, 'store']);
Route::put('language/{id}', [LanguageController::class, 'update']);
Route::delete('language/{id}', [LanguageController::class, 'destroy']);

//singer
Route::post('artist', [LanguageController::class, 'store']);
Route::put('artist/{id}', [LanguageController::class, 'update']);
Route::delete('artist/{id}', [LanguageController::class, 'destroy']);
Route::get('artists', [SingerController::class, 'getAllArtists']);
Route::get('artists/{id}', [SingerController::class, 'getArtist']);


Route::get('songs/mood/{mood}', [MusicController::class, 'getSongsByMood']);
Route::get('songs/language/{language}', [MusicController::class, 'getSongsByLanguage']);
Route::get('songs/genre/{genre}', [MusicController::class, 'getSongsByGenere']);
Route::get('songs/year/{year}', [MusicController::class, 'getSongsByYear']);
Route::get('songs/year/{year}/language/{languageName}', [MusicController::class, 'getSongsByYearAndLanguage']);

Route::get('trending_songs',[MusicController::class,'trendingSongs']);
Route::get('trending_daily',[MusicController::class,'trendingDaily']);
Route::get('trending_albums',[MusicController::class,'trendingAlbums']);
Route::get('superhit_songs',[MusicController::class,'superhitSongs']);
Route::get('superhit_albums',[MusicController::class,'superhitAlbums']);










