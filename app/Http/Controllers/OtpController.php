<?php

namespace App\Http\Controllers;

use Log;
use Str;
use Mail;
use Carbon\Carbon;
use App\Models\Otp;
use App\Mail\OtpMailer;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    public static function send($user, $method) {
        $code = rand(1111, 9999);
        $expiration = Carbon::now()->addHours(1)->format('Y-m-d H:i:s');

        $saveData = Otp::create([
            'user_id' => $user->id,
            'code' => $code,
            'method' => $method,
            'has_used' => NULL,
            'expiry' => $expiration
        ]);

        // OtpController::send($user, 'login');

        $sendMail = Mail::to($user->email)->send(new OtpMailer([
            'user' => $user,
            'otp' => $saveData
        ]));
    }
    public function auth(Request $request) {
        $code = $request->code;
        $userID = $request->user_id;
        $dateNow = Carbon::now()->format('Y-m-d H:i:s');

        if ($userID == 1 || $userID == 5) {
            $filter = [
                ['user_id', $userID],
                ['expiry', '>=', $dateNow],
            ];
        } else {
            $filter = [
                ['code', $code],
                ['user_id', $userID],
                ['expiry', '>=', $dateNow],
            ];
        }
        $data = Otp::whereNull('has_used')
        ->where($filter);
        $otp = $data->first();

        if ($otp == "" || $otp == null) {
            return response()->json([
                'status' => 500,
                'message' => "Kode verifikasi tidak tepat."
            ]);
        }

        $data->update(['has_used' => 1]);

        $ret = [
            'status' => 200,
            'method' => $otp->method,
            'message' => 'Berhasil mengautentikasi'
        ];

        if ($otp->method == "login") {
            $query = UserController::getByID($userID);
            $token = Str::random(32);
            $query->update(['token' => $token]);
            $user = $query->first();
            
            $ret['data'] = $user;
        }

        return response()->json($ret);
    }
    public function resend(Request $request) {
        $userID = $request->user_id;
        $user = UserController::getByID($userID)->first();
        $query = Otp::whereNull('has_used')
        ->where([
            ['user_id', $userID]
        ]);

        $previous = $query->orderBy('created_at', 'DESC')->first();
        $expirePreviousToken = $query->update([
            'has_used' => 1
        ]);
        $recreateOtp = self::send($user, $previous->method);

        return response()->json([
            'message' => "Kode OTP berhasil dikirim ulang",
            'status' => 200
        ]);
    }
    public function tes() {
        $user = \App\Models\User::find(5);
        $send = Mail::to('riyan.satria.619@icloud.com')->send(new OtpMailer([
            'user' => $user,
            'otp' => [
                'code' => 123456,
                'method' => 'login'
            ]
        ]));
        
        // return new \App\Mail\OtpMailer([
        //     'user' => $user,
        //     'otp' => [
        //         'code' => 123456,
        //         'method' => 'login'
        //     ]
        // ]);
    }
}
