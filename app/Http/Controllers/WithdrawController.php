<?php

namespace App\Http\Controllers;

use Xendit\Xendit as Xendit;
use Illuminate\Http\Request;

use App\Models\UserWithdraw;
use App\Models\VisitorOrder;
use App\Models\UserBank as Bank;

class WithdrawController extends Controller
{
    public function history(Request $request) {
        $token = $request->token;
        $user = UserController::get($token)->first();
        $datas = UserWithdraw::where('user_id', $user->id)->orderBy('created_at', 'DESC')
        ->with('bank')->get();

        return response()->json([
            'status' => 200,
            'datas' => $datas
        ]);
    }
    public function payout(Request $request) {
        $token = $request->token;
        $user = UserController::get($token)->first();
        $externalID = $user->id."_".time();

        $bank = Bank::where('id', $request->bank_id)->first();

        $makePayout = UserWithdraw::create([
            'external_id' => $externalID,
            'user_id' => $user->id,
            'bank_id' => $request->bank_id,
            'amount' => $request->amount,
            'status' => 'pending'
        ]);

        $setAllOrder = VisitorOrder::where([
            ['user_id', $user->id],
            ['has_withdrawn', 0]
        ])->update(['has_withdrawn' => 1]);

        // SUBMIT TO XENDIT
        $makeDisbursement = \Xendit\Disbursements::create([
            'external_id' => $externalID,
            'amount' => $request->amount,
            // 'amount' => 500,
            'bank_code' => strtoupper($bank->bank_name),
            'account_holder_name' => $bank->name,
            'account_number' => $bank->number,
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Berhasil meminta withdraw. Mohon tunggu 2 x 24 jam untuk proses pencairan dana"
        ]);
    }
    public function tes() {
        Xendit::setApiKey(env('XENDIT_SECRET_KEY'));
        $banks = \Xendit\Disbursements::getAvailableBanks();
        return $banks;
    }
}
