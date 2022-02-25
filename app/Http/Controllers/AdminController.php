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
    public function setEnv($datas) {
        $path = base_path('.env');

        if (file_exists($path)) {
            foreach ($datas as $key => $value) {
                $oldKey = env($key);

                // Checking if contain space
                $s = explode(" ", $value);
                if (isset($s[1])) {
                    $value = "\"$value\"";
                }
                if (isset(explode(" ", $oldKey)[1])) {
                    $oldKey = "\"$oldKey\"";
                }
                
                $patt = "$key=$oldKey";
                $repl = "$key=$value";
                file_put_contents($path, str_replace($patt, $repl, file_get_contents($path)));
            }
        }
    }
    public function getSettings(Request $request) {
        $configs = $request->configs;

        $ret = [];
        foreach ($configs as $cfg) {
            $ret[$cfg] = env($cfg);
        }
        return response()->json($ret);
    }
    public function setSettings(Request $request) {
        $configs = $request->configs;
        $this->setEnv($configs);
        return response()->json(['status' => $configs]);
    }
}
