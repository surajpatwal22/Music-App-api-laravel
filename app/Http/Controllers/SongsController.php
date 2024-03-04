<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SongsController extends Controller
{
    public function upload(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'subtitle' => 'required',
            'artist' => 'required',
            'audio_file' => 'required|mimes:mp3|max:10240',
            'image_file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'status' => 400,
                'success' => false
            ]);
        } else {

            $audioFile = $request->file('audio_file');
            $audioFileName = time() . '.'.$audioFile->getClientOriginalName();
            $audioFilePath = public_path() . '/audio';
            $audioFile->move($audioFilePath, $audioFileName);

            $imageFile = $request->file('image_file');
            $imageFileName = time() . '_' . $imageFile->getClientOriginalName();
            $imagePath = public_path() . '/song_image';
            $imageFile->move($imagePath, $imageFileName);

            $song = Song::create([
                'title' => $request->input('title'),
                'subtitle' => $request->input('subtitle'),
                'artistname' => $request->input('artist'),
                'file' => '/audio/' . $audioFileName,
                'image' => '/song_image/' . $imageFileName
            ]);

            $song->save();

            return response()->json([
                'message' => 'song updated successfully',
                'status' => 200,
                'success' => true
            ], 200);
        }

    }

    public function getAllSongs(Request $request)
        {
            $songs = Song::orderBy("created_at", "desc")->get();
            return response()->json([
               
                "success" => true,
                "songs" => $songs
            ],200);
        }
    

}
