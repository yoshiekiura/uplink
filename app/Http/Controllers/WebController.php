<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebController extends Controller
{
    public function landingPage() {
        return view('user.landing');
    }
    public function CheckoutPage() {
        return view('user.checkout');
    }
}
