<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AlbumController extends Controller
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
                $imageName = time() . '.' . $file->getClientOriginalName();
                $imageName = str_replace(' ', '_', $imageName);
                $imagePath = public_path() . '/storage/photos/album/';
                $file->move($imagePath, $imageName);
            } else {
                return response()->json([
                    'message' => 'Image field is required!',
                    'status' => 400,
                    'success' => false
                ],400);
            }
            
            $album = Album::create([
                'name' => $request->title,
                'status' => $request->status,
                'image' => 'public/storage/photos/album/' . $imageName
            ]);

            $album->save();
            if ($album) {
                return response()->json([
                    'message' => 'Album created successfully',
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
        

    }

    public function update(Request $request, $id)
    {
        $album = Album::findOrFail($id);
        if ($album) {
          
            if (($request->hasFile('photo'))) {
                $file = $request->photo;
                $imageName = time() . '.' . $file->getClientOriginalName();
                $imageName = str_replace(' ', '_', $imageName);
                $imagePath = public_path() . '/storage/photos/album/';
                $file->move($imagePath, $imageName);
                $album->image = 'public/storage/photos/album/' . $imageName;
        }

            if ($request->filled('name')) {
                $album->name = $request->name;
            }
            if ($request->filled('status')) {
                $album->status = $request->status;
            }

            $album->save();

            return response()->json([
                'message' => 'Album updated successfully',
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
        $album = Album::findOrFail($id);
      
        if($album){
            $album->delete();
            return response()->json([
                'message' => 'Album deleted successfully',
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


    public function getAllAlbum(){

        $album = Album::with(['songs'])->orderBy("created_at", "desc")->get();
        return response()->json([
            "success" => true,
            "data" => $album
        ],200);
    }

    public function getAlbum($id){
        $album = Album::with(['songs'])->find($id);;
        return response()->json([
            "success" => true,
            "data" => $album
        ],200);
    }



}
