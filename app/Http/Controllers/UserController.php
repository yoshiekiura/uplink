<?php

namespace App\Http\Controllers;

use Str;
use Auth;
use Hash;
use Storage;
use Validator;
use App\Models\User;
use App\Models\UserSite;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public static function get($token) {
        return User::where('token', $token);
    }
    public static function getByID($id) {
        return User::where('id', $id);
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
			if ($user->icon == "default") {
                $user->icon = asset('images/default-icon.png');
            } else {
                $user->icon = asset('storage/user_icon/'.$user->icon);
            }
            $response['status'] = 200;
            $response['data'] = $user;
            $response['token'] = $token;
        }

        return response()->json($response);
    }
    public function profile($username) {
        $user = User::where('username', $username)->first();
        $user->icon = asset('storage/user_icon/' . $user->icon);
        
        return response()->json([
            'status' => 200,
            'user' => $user,
            'message' => "Berhasil mengambil profil"
        ]);
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
        $user = $data->first();
        $otpMethod = $user->is_email_activated == NULL ? 'register' : 'login';

        $sendOtp = OtpController::send($user, $otpMethod);

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
            return response()->json(['status' => 500, 'message' => $validateData->messages()]);
        }

        $icon = "default";
        $background = "default";
        $title = $request->name;
        $bio = "I just found this wonderful app";

        $registering = User::create([
            'name' => $name,
            'username' => $request->username,
            'email' => $request->email,
            'bio' => $bio,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
            'icon' => $icon,
            'background_image' => $background,
        ]);

        $saveSettings = UserSite::create([
            'user_id' => $registering->id,
            'seo_title' => $name . " - Uplink.id",
            'seo_description' => $bio
        ]);

        $sendOtp = OtpController::send($registering, 'register');

        return response()->json(['status' => 200, 'message' => "Berhasil mendaftar", 'data' => $registering]);
    }
    public function registerCompletion(Request $request) {
        $id = $request->id;
        $token = Str::random(32);

        $data = User::where('id', $id);
        $updateData = $data->update([
            'categories' => $request->categories,
            'is_email_activated' => 1,
            'token' => $token
        ]);

        return response()->json(['status' => 200, 'message' => "Berhasil memenuhi pendaftaran", 'data' => $data->first()]);
    }
    public function logout(Request $request) {
        $token = $request->token;
        $loggingOut = self::get($token)->update(['token' => null]);
        
        return response()->json(['status' => 200, 'message' => "Berhasil logout"]);
    }
    public function update(Request $request) {
        $token = $request->token;

        if ($token != "") {
            $data = User::where('token', $token);
            $user = $data->first();

            $toUpdate = [
                'name' => $request->name,
                'bio' => $request->bio,
            ];
    
            if ($request->isChangingIcon == 1 || $request->isChangingIcon != 0) {
                $icon = $request->file('icon');
                $iconFileName = $icon->getClientOriginalName();
                $toUpdate['icon'] = $iconFileName;
                $icon->storeAs('public/user_icon', $iconFileName);
                $deleteOldIcon = Storage::delete('public/user_icon/'.$user->icon);
            }

            $updateData = $data->update($toUpdate);
        }

        return response()->json([
            'status' => 200,
            'message' => "Berhasil mengubah profil",
            'data' => $user
        ]);
    }
}
