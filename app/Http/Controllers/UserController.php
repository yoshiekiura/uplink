<?php

namespace App\Http\Controllers;

use Str;
use Auth;
use Hash;
use Mail;
use Storage;
use Redirect;
use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserSite;
use App\Models\Admin;
use App\Models\UserPremium;
use App\Mail\RegisterByWeb;
use App\Models\VisitorOrder;
use App\Mail\PaymentComplete;
use App\Mail\ContactMessage as ContactMessageMailer;
use App\Models\ContactMessage;
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
            $relations = explode(",", $request->with);
            $data = $data->with($relations);
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
        $user = User::where('username', $username)->with('socials')->first();
        if ($user != "") {
            $user->icon = asset('storage/user_icon/' . $user->icon);
        }
        
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
    public function signup($props) {
        $phone = array_key_exists('phone', $props) ? $props['phone'] : null;
        $bio = "I just found this wonderful app";
        $saveData = User::create([
            'name' => $props['name'],
            'username' => $props['username'],
            'email' => $props['email'],
            'bio' => $bio,
            'password' => bcrypt($props['password']),
            'phone' => $phone,
            'icon' => "default",
            'background_image' => "default",
        ]);

        $saveSettings = UserSite::create([
            'user_id' => $saveData->id,
            'seo_title' => $props['name'] . " - Uplink.id",
            'seo_description' => $bio
        ]);

        return $saveData;
    }
    public function webRegister(Request $request) {
        $username = $request->username;
        $email = $request->email;
        $name = $username;
        $registering = $this->signup([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => $request->password,
        ]);
        
        $sendEmail = Mail::to($email)->send(new RegisterByWeb($registering));

        return response()->json([
            'status' => 200,
        ]);
    }
    public function usernameCheck(Request $request) {
        $res['status'] = 200;
        $users = User::where('username', $request->username)->get()->count();
        if ($users > 0) {
            $res['status'] = 500;
        }
        return response()->json($res);
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
        $name = $request->name;
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
    public function forgotPassword(Request $request) {
        $email = $request->email;
        $data = User::where('email', $email);
        $user = $data->first();

        if ($user == "") {
            $res['status'] = 404;
            $res['message'] = "We cannot find any user with email address ".$email;
        } else {
            $sendOtp = OtpController::send($user, 'forgot-password');
            $res['status'] = 200;
            $res['message'] = "We cannot find any user with email address ".$email;
        }

        return response()->json($res);
    }
    public function resetPassword(Request $request) {
        $data = User::where('email', $request->email);
        $user = $data->first();

        $data->update([
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Password has been changed. Please login again using new password (".$request->email." : ".$request->password.")"
        ]);
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
    public function getBalance(Request $request) {
        $token = $request->token;
        $user = self::get($token)->first();
        $getOrders = VisitorOrder::where([
            ['user_id', $user->id],
            ['is_placed', 1],
            ['payment_status', 1],
            ['has_withdrawn', 0]
        ])->get();
        $balance = $getOrders->sum('grand_total');

        return response()->json([
            'status' => 200,
            'balance' => $balance
        ]);
    }
    public function getBank(Request $request) {
        $token = $request->token;
        $user = self::get($token)->first();
    }
    public function saveSite(Request $request) {
        $token = $request->token;
        $user = UserController::get($token)->first('id');
        $updateSettings = UserSite::where('user_id', $user->id)->update([
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'analytics_tracking_id' => $request->analytics_tracking_id,
            'pixel_tracking_id' => $request->pixel_tracking_id,
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Pengaturan baru berhasil disimpan"
        ]);
    }
    public function getPremium(Request $request) {
        $token = $request->token;
        $user = UserController::get($token)->with('premium')->first();
        $now = Carbon::now();

        if ($user->premium != null) {
            $activeUntil = Carbon::parse($user->premium->active_until);
            $startDate = $now <= $activeUntil ? $activeUntil : $now;
        } else {
            $startDate = $now;
        }
        $monthQuantity = $request->plan == 'monthly' ? 1 : 12;
        $newExpiration = $startDate->addMonths($monthQuantity);

        $savePremium = UserPremium::create([
            'user_id' => $user->id,
            'active_until' => $newExpiration,
            'month_quantity' => $monthQuantity,
            'payment_amount' => 100,
            'payment_status' => 'success',
            'payment_method' => 'bri'
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Pengaturan baru berhasil disimpan"
        ]);
    }
    public function tes() {
        // return 
        $sendMail = Mail::to('riyan.satria.619@gmail.com')->send(new PaymentComplete());
    }
    public function delete($id, $referrer = NULL) {
        $data = User::where('id', $id);
        $user = $data->with(['events','links'])->first();
        
        $deleteData = $data->delete();
        $deleteIcon = Storage::delete('public/user_icon/' . $user->icon);
        
        if ($user->events->count() != 0) {
            foreach ($user->events as $event) {
                $deleteIcon = Storage::delete('public/event_cover/' . $event->cover);
            }
        }
        if ($user->links->count() != 0) {
            foreach ($user->links as $link) {
                $deleteIcon = Storage::delete('public/link_image/' . $link->image);
            }
        }

        if ($referrer == "admin") {
            // return Redirect::back()->with(['message' => "User has been deleted"]);
            return redirect()->away('https://admin.uplink.id/user/seller');
        }
        return response()->json([
            'status' => 200,
        ]);
    }
    public function contact(Request $request) {
        $saveData = ContactMessage::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
            'has_read' => 0,
        ]);

        $admins = Admin::get();
        foreach ($admins as $admin) {
            $sendToEmail = Mail::to($admin->email)->send(new ContactMessageMailer([
                'admin' => $admin,
                'data' => $saveData,
            ]));
        }

        return response()->json([
            'status' => 200,
            'message' => "Terima kasih telah menghubungi Uplink.id, tim kami akan segera menghubungi Anda"
        ]);
    }
}
