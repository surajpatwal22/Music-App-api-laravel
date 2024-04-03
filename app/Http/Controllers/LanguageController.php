<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
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
            
            $language = Language::create([
                'name' => $request->title,
                'status' => $request->status,
            ]);

            $language->save();
            if ($language) {
                return response()->json([
                    'message' => 'language created successfully',
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
        $language = Language::findOrFail($id);
        if ($language) {
          

            if ($request->filled('name')) {
                $language->name = $request->name;
            }
            if ($request->filled('status')) {
                $language->status = $request->status;
            }

            $language->save();

            return response()->json([
                'message' => 'language updated successfully',
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
        $language = Language::findOrFail($id);
      
        if($language){
            $language->delete();
            return response()->json([
                'message' => 'language deleted successfully',
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
