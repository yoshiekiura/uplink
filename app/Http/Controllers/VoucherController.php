<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Models\UserVoucher as Voucher;
use App\Models\VisitorOrder;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function get(Request $request) {
        $token = $request->token;
        $user = UserController::get($token)->first();

        $vouchers = Voucher::where('user_id', $user->id)->orderBy('expiration', 'DESC')->get();

        return response()->json([
            'status' => 200,
            'vouchers' => $vouchers
        ]);
    }
    public function store(Request $request) {
        $token = $request->token;
        $user = UserController::get($token)->first();

        $saveData = Voucher::create([
            'user_id' => $user->id,
            'code' => $request->code,
            'amount' => $request->amount,
            'quantity' => $request->quantity,
            'discount_type' => $request->type,
            'expiration' => $request->expiration,
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Berhasil menambahkan voucher baru"
        ]);
    }
    public function delete(Request $request) {
        $id = $request->id;
        $data = Voucher::where('id', $id);
        $deleteData = $data->delete();

        return response()->json([
            'status' => 200,
            'message' => "Berhasil menghapus voucher"
        ]);
    }
    public function apply(Request $request) {
        $code = $request->code;
        $voucher = Voucher::where([
            ['code', $code],
            ['user_id', $request->user_id]
        ])->orderBy('created_at', 'DESC')->first();
        
        $statusCode = $voucher != "" ? 200 : 404;

        if ($voucher != "") {
            $cartID = $request->cart_id;
            $cartQuery = VisitorOrder::where('id', $cartID);
            $cart = $cartQuery->first();
            $grandTotal = $cart->grand_total;

            if ($voucher->discount_type == 'fixed') {
                $newGrandTotal = $grandTotal - $voucher->amount;
            } else {
                $amountToDiscount = $voucher->amount / 100 * $grandTotal;
                $newGrandTotal = $grandTotal - $amountToDiscount;
            }
            
            $useVoucher = $cartQuery->update([
                'voucher_id' => $voucher->id,
                'grand_total' => $newGrandTotal,
            ]);
        }

        return response()->json([
            'status' => $statusCode,
        ]);
    }
    public function remove(Request $request) {
        $cartQuery = VisitorOrder::where('id', $request->cart_id);
        $cart = $cartQuery->with('voucher')->first();
        $voucher = $cart->voucher;

        if ($voucher->discount_type == 'fixed') {
            $newGrandTotal = $cart->grand_total + $voucher->amount;
        } else {
            $newGrandTotal = 100 / (100 - $voucher->amount) * $cart->grand_total;
        }
        
        $cartQuery->update([
            'voucher_id' => null,
            'grand_total' => $newGrandTotal
        ]);

        return response()->json([
            'status' => 200
        ]);
    }
    public function statistic(Request $request) {
        $now = Carbon::now();
        $token = $request->token;
        $user = UserController::get($token)->first();

        $datas = VisitorOrder::where([
            ['voucher_id', 'IS NOT', NULL],
            ['user_id', $user->id]
        ])
        ->whereBetween('created_at', [
            $now->startOfMonth()->format('Y-m-d'),
            $now->endOfMonth()->format('Y-m-d')
        ])
        ->with(['voucher'])
        ->get();

        return response()->json([
            'status' => 200,
            'datas' => $datas
        ]);
    }
}
