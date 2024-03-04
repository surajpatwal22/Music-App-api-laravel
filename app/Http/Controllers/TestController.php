<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index(Request $request){
        $term = $request->term;

        DB::table('test_terms')->insert([
            'term' => $term
        ]);
        return response()->json([
            'message' => 'term saved',
            'status' => 200 ,
            'success' => true
        ]);
    }
}
