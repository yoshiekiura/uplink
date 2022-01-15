<?php

namespace App\Http\Controllers;

use Str;
use Auth;
use Hash;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public static function get($token) {
        return User::where('token', $token);
    }
    public function me(Request $request) {
        $response = [
            'status' => 500,
            'data' => null
        ];
        
        $token = $request->token;
        $data = User::where('token', $token);
        if ($request->with != "") {
            $data = $data->with($request->with);
        }
        $user = $data->first();
        if ($user != "") {
            $response['status'] = 200;
            $response['data'] = $user;
        }

        return response()->json($response);
    }
    public static function authenticate($token, $getQuery = null) {
        $user = User::where('token', $token);
        if ($user->first() == "") return false;
        if ($getQuery != null) {
            return $user->first();
        }
        return $user;
    }
    public function login(Request $request) {
        $data = User::where('email', $request->email);
        $user = $data->first();
        if ($user == "") {
            return response()->json(['status' => 500, 'message' => "Email Anda belum terdaftar"]);
        }
        $loggingIn = Hash::check($request->password, $user->password);

        if (!$loggingIn) {
            return response()->json(['status' => 500, 'message' => "Kombinasi email dan password tidak tepat"]);
        }

        $token = Str::random(32);
        $data->update(['token' => $token]);
        $user = $data->first();

        return response()->json([
            'status' => 200,
            'message' => "Login berhasil",
            'data' => $user,
        ]);
    }
    public function register(Request $request) {
        $customMessagesValidator = [
            'required' => ":attribute harus diisi",
            'unique' => ":attribute telah digunakan oleh orang lain. Mohon gunakan :attribute yang berbeda",
            'min' => ":attribute harus minimal 6 karakter"
        ];

        $validateData = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'username' => 'required|min:6|unique:users',
            'password' => 'required|min:6',
            'phone' => 'required|unique:users',
        ], $customMessagesValidator);
        
        if ($validateData->fails()) {
            return response()->json(['status' => 500, 'data' => $validateData->messages()]);
        }

        $icon = "default-icon.png";
        $background = "default-background.png";
        $token = Str::random(16);

        $registering = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
            'icon' => $icon,
            'token' => $token,
            'background_image' => $background,
        ]);

        return response()->json(['status' => 200, 'message' => "Berhasil mendaftar", 'user' => $registering]);
    }
    public function registerCompletion(Request $request) {
        $token = $request->token;
        $data = User::where('token', $token);
        $updateData = $data->update([
            'categories' => $request->categories
        ]);

        return response()->json(['status' => 200, 'message' => "Berhasil memenuhi pendaftaran", 'user' => $data->first()]);
    }
    public function logout(Request $request) {
        $token = $request->token;
        $loggingOut = self::get($token)->update(['token' => null]);
        
        return response()->json(['status' => 200, 'message' => "Berhasil logout"]);
    }
}
