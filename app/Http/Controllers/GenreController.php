<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GenreController extends Controller
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
            
            $genre = Genre::create([
                'name' => $request->title,
                'status' => $request->status,
            ]);

            $genre->save();
            if ($genre) {
                return response()->json([
                    'message' => 'Genre created successfully',
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
        $genre = Genre::findOrFail($id);
        if ($genre) {
          

            if ($request->filled('name')) {
                $genre->name = $request->name;
            }
            if ($request->filled('status')) {
                $genre->status = $request->status;
            }

            $genre->save();

            return response()->json([
                'message' => 'genre updated successfully',
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
        $genre = Genre::findOrFail($id);
      
        if($genre){
            $genre->delete();
            return response()->json([
                'message' => 'genre deleted successfully',
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



}
