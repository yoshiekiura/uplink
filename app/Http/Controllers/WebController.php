<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Link;
use App\Models\UserCategory;
use Illuminate\Http\Request;

class WebController extends Controller
{
    public function landingPage($username, Request $request) {
        $user = User::where('username', $username)->first();
        if ($user == "") {
            return view('errors.404');
        }
        $type = $request->item_type == "" ? "links" : $request->item_type;

        $categories = UserCategory::where('user_id', $user->id)->with($type)->get();
        
        return view('user.landing', [
            'user' => $user,
            'type' => $type,
            'categories' => $categories
        ]);
    }
    public function CheckoutPage() {
        return view('user.checkout');
    }
}
