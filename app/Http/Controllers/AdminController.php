<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Admin;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public static function me() {
        $myData = Auth::guard('admin')->user();
        return $myData;
    }
    public function loginPage(Request $request) {
        return view('admin.login', [
            'request' => $request
        ]);
    }
    public function login(Request $request) {
        $loggingIn = Auth::guard('admin')->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if (!$loggingIn) {
            return redirect()->route('admin.loginPage')->withErrors(['Kombinasi email dan password tidak tepat']);
        }
        
        return redirect()->route('admin.dashboard');
    }
    public function logout() {
        $loggingOut = Auth::guard('admin')->logout();
        return redirect()->route('admin.loginPage')->with(['message' => "Berhasil logout"]);
    }
    public function dashboard() {
        $myData = self::me();

        return view('admin.dashboard', [
            'myData' => $myData
        ]);
    }
}
