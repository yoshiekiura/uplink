<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function register(Request $request) {
        $token = Str::random(32);
        $saveData = Visitor::create([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->json($saveData);
    }
}
