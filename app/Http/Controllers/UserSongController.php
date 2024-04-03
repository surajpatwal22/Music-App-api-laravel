<?php

namespace App\Http\Controllers;

use App\Models\UserSong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserSongController extends Controller
{
    public function SongListen(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'song_id' => 'required|exists:songs,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'status' => 400,
                'success' => false
            ], 400);
        } else {
            $usersong = UserSong::create([
                'user_id' => $user->id,
                'song_id' => $request->input('song_id')
            ]);

            return response()->json([
                'message' => 'Song Listen by user',
                'status' => 200,
                'success' => true,
                'data' => $usersong
            ], 200);
        }
    }
    public function getUserSong(Request $request)
    {
        $user = Auth::user();

        $userSongs = UserSong::where('user_id', $user->id)->with('song')->orderBy('created_at', 'desc')->get();
        // dd($userSongs);
        if ($userSongs) {
            return response()->json([
                'message' => 'User songs retrieved successfully',
                'status' => 200,
                'success' => true,
                'UserSongs' => $userSongs
            ], 200);
        } else {
            return response()->json([
                'message' => 'Something went wrong',
                'status' => 400,
                'success' => false,
            ], 400);
        }

    }

}
