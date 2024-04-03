<?php

namespace App\Http\Controllers;

use App\Models\Singer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SingerController extends Controller
{

    public function store(Request $request)
    {

        $validator = Validator::make(request()->all(), [
            'title' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'status' => 400,
                'success' => false
            ]);
        } else {
            if ($request->photo) {
                $file = $request->photo;
                $imageName = $file->getClientOriginalName();
                $imageName = str_replace(' ', '_', $imageName);
                $imagePath = public_path() . '/storage/photos/singer/';
                $file->move($imagePath, $imageName);
            } else {
                return response()->json([
                    'message' => 'Image field is required!',
                    'status' => 400,
                    'success' => false
                ]);
            }
            $singer = Singer::create([
                'name' => $request->title,
                'status' => $request->status,
                'Image' => 'public/storage/photos/singer/' . $imageName
            ]);

            $singer->save();
            if ($singer) {
                return response()->json([
                    'message' => 'singer created successfully',
                    'status' => 200,
                    'success' => true
                ], 200);
            } else {
                return response()->json([
                    'message' => 'something went wrong',
                    'status' => 400,
                    'success' => false
                ], 400);
            }


        }
        ;

    }

    public function update(Request $request, $id)
    {
        $singer = Singer::findOrFail($id);
        if ($singer) {
            if (($request->hasFile('photo'))) {
                    $file = $request->photo;
                    $imageName = $file->getClientOriginalName();
                    $imageName = str_replace(' ', '_', $imageName);
                    $imagePath = public_path() . '/storage/photos/singer/';
                    $file->move($imagePath, $imageName);
                    $singer->Image = 'public/storage/photos/singer/' . $imageName;
            }

            if ($request->filled('name')) {
                $singer->name = $request->name;
            }

            $singer->save();

            return response()->json([
                'message' => 'singer updated successfully',
                'status' => 200,
                'success' => true
            ], 200);
        }
        else{
            return response()->json([
                'message' => 'something went wrong',
                'status' => 400,
                'success' => false
            ], 400);
        } 
    }

    
    public function destroy($id)
    {
        $singer = Singer::findOrFail($id);
      
        if($singer){
            $singer->delete();
            return response()->json([
                'message' => 'singer deleted successfully',
                'status' => 200,
                'success' => true
            ], 200);
        }
        else{
            return response()->json([
                'message' => 'something went wrong',
                'status' => 400,
                'success' => false
            ], 400);

        }
    }

    public function getAllArtists()
    {
        $artists = Singer::with(['songs'])->orderBy("created_at", "asc")->get();
        return response()->json([
            "success" => true,
            "data" => $artists
        ],200);
    }

    public function getArtist($id){
        $artist = Singer::with(['songs'])->find($id);;
        return response()->json([
            "success" => true,
            "data" => $artist
        ],200);
    }
}
