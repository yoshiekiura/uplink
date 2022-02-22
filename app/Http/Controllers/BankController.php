<?php

namespace App\Http\Controllers;

use App\Models\UserBank as Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function get(Request $request) {
        $user = UserController::get($request->token)->first();
        $banks = Bank::where([
            ['user_id', $user->id]
        ])->orderBy('created_at', 'DESC')->get();

        return response()->json([
            'status' => 200,
            'user_id' => $user->id,
            'message' => "Berhasil mengambil data bank",
            'data' => $banks
        ]);
    }
    public function store(Request $request) {
        $token = $request->token;
        $user = UserController::get($token)->first();

        $saveData = Bank::create([
            'user_id' => $user->id,
            'bank_name' => $request->bank_name,
            'bank_code' => $request->bank_code,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Bank berhasil ditambahkan"
        ]);
    }
    public function delete(Request $request) {
        $token = $request->token;
        $id = $request->id;
        
        $user = UserController::get($token)->first();
        $data = Bank::where('id', $id);
        $bank = $data->first();

        if ($bank->user_id != $user->id) {
            return response()->json([
                'status' => 403,
                'message' => "Anda tidak dapat menghapus akun ini"
            ]);
        }

        $deleteData = $data->delete();

        return response()->json([
            'status' => 200,
            'message' => "Berhasil menghapus data bank"
        ]);
    }
}
