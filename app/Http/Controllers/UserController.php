<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make(request()->all(), [
            'mobile_number' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'status' => 400,
                'success' => false
            ]);
        } else
       {
        $user = User::where('mobile_no', $request->mobile_number)->first();

        if (!$user) {
            $newUser = User::create([
                'mobile_no' => $request->mobile_number,
            ]);

            $token = $newUser->createToken('Personal Access Token', ['expires' => now()->addDays(7)])->plainTextToken;

            return response()->json([
                'message' => 'Registered and logged in successfully',
                'token' => $token,
                'status' => 200,
                'user' => $newUser,
                'success' => true
            ], 200);
        } else {
            $token = $user->createToken('Personal Access Token', ['expires' => now()->addDays(7)])->plainTextToken;

            return response()->json([
                'message' => 'Logged in successfully',
                'token' => $token,
                'status' => 200,
                'user' => $user,
                'success' => true
            ], 200);
        }
    }
    }

    public function updateProfile(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($user) {
            $validator = Validator::make($request->all(), [
                'email' => 'email',
                'contact' => 'min:10|max:10',
                'name' => 'string'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors(),
                    'status' => 400,
                    'success' => false
                ]);
            } else {
                if ($request->file) {
                    try {
                        $file = $request->file('file');
                        $imageName = time() . '.' . $file->extension();
                        $imagePath = public_path() . '/user_profile';

                        $file->move($imagePath, $imageName);

                        $user->profile = '/user_profile/' . $imageName;
                    } catch (Exception $e) {
                        return $e;
                    }
                }

                $user->email = $request->email;
                if ($request->contact) {
                    $user->contact = $request->contact;
                }
                $user->name = $request->name;
                if ($request->bio) {
                    $user->bio = $request->bio;
                }
                $user->save();

                return response()->json([
                    'message' => 'updated successfully',
                    'status' => 200,
                    'success' => true
                ],200);
            }

        } else {
            return response()->json([
                'message' => 'user not found',
                'status' => 404,
                'success' => false
            ],404);
        }
    }

}
