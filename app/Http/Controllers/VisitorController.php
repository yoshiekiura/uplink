<?php

namespace App\Http\Controllers;

use Str;
use Carbon\Carbon;
use App\Models\Visitor;
use App\Models\VisitorOrder;
use App\Models\VisitorOrderDetail;
use App\Models\Link;
use App\Models\LinkStat;
use App\Models\User;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function me(Request $request) {
        $token = $request->token;
        $visitor = Visitor::where('token', $token)->first();

        return response()->json(['data' => $visitor,'token' => $token]);
    }
    public function check(Request $request) {
        $token = $request->token;
        $userID = $request->user_id;
        
        $users = Visitor::where('token', $token)->get();
        $isDataFound = false;
        $isRegistered = false;
        $newToken = null;

        if ($users->count() != 0) {
            $isRegistered = true;
            foreach ($users as $key => $item) {
                if ($item->user_id == $userID) {
                    $isDataFound = $key; // data found no need to register
                }
            }

            if ($isDataFound === false) {
                $newToken = Str::random(32);
                $registering = Visitor::create([
                    'token' => $newToken,
                    'user_id' => $userID,
                    'name' => $users[0]->name,
                    'email' => $users[0]->email,
                    'phone' => $users[0]->phone,
                    'address' => $users[0]->address,
                ]); // data exist but with different userID
            }
        }

        return response()->json([
            'isDataFound' => $isDataFound,
            'token' => $newToken,
            'isRegistered' => $isRegistered
        ]);
    }
    public function register(Request $request) {
        $token = Str::random(32);
        $saveData = Visitor::create([
            'user_id' => $request->profile_id,
            'name' => $request->name,
            'token' => $token
        ]);

        return response()->json([
            'data' => $saveData,
            'status' => 200,
            'token' => $token
        ]);
    }
    public function visitLink($id, Request $request) {
        $today = Carbon::now()->format('Y-m-d');
        $token = $request->token;
        $visitor = Visitor::where('token', $token)->first();
        
        $data = LinkStat::where([
            ['link_id', $id],
            ['visitor_id', $visitor->id],
            ['date', $today]
        ])->with('link');

        $stat = $data->first();
        if ($stat == "") {
            $createData = LinkStat::create([
                'link_id' => $id,
                'visitor_id' => $visitor->id,
                'date' => $today,
                'count' => 1
            ]);
            $link = Link::where('id', $id)->first();
        } else {
            $data->increment('count');
            $link = $stat->link;
        }

        return response()->json([
            'status' => 200,
        ]);
    }
    public function update(Request $request) {
        $data = Visitor::where('id', $request->visitor_id);
        $visitor = $data->first();
        $updateData = $data->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
        
        return response()->json([
            'status' => 200,
            'message' => "Berhasil mengubah data visitor " . $visitor->name
        ]);
    }
    public function transactions(Request $request) {
        $token = $request->token;
        $username = $request->username;
        $user = User::where('username', $username)->first();

        $visitor = Visitor::where('token', $token)->with('transactions')
        ->whereHas('transactions', function ($query) use($user) {
            $query->where([
                ['is_placed', 1],
                ['user_id', $user->id]
            ]);
        })
        ->first();

        return response()->json([
            'status' => 200,
            'visitor' => $visitor
        ]);
    }
    public function transactionDetail($id, Request $request) {
        $data = VisitorOrder::where('id', $id)
        ->with(['details','visitor'])
        ->first();

        $details = $data->details;
        foreach ($details as $item) {
            $productType = $item->product_type;
            $classModel = CartController::$classToCall[$productType];
            $className = $classModel['name'];
            $queryProduct = $className::where('id', $item->{$productType});
            if (array_key_exists('relation', $classModel)) {
                $queryProduct = $queryProduct->with($classModel['relation']);
            }
            $item->product = $queryProduct->first();
        }

        return $data;
        // return response()->json()
    }
    public function paymentCallbacks($channel, Request $request) {
        $amount = $request->amount;
        $data = $request->data;
        $referenceID = $data['reference_id'];
        $status = $data['status'];

        $cartQuery = VisitorOrder::where('payment_reference_id', $referenceID);
        $updateCart = $cartQuery->update(['payment_status' => $status]);

        return response()->json([
            'message' => "Halo ".$channel,
            'reference_id' => $referenceID
        ]);
    }
    public function statistic(Request $request) {
        $token = $request->token;
        $now = Carbon::now();
        
        $user = UserController::get($token)->first();
        $query = Visitor::where('user_id', $user->id);
        $customers = $query->get('id');

        $thisMonth = $query->whereBetween('created_at', [
            $now->startOfMonth()->format('Y-m-d'),
            $now->endOfMonth()->format('Y-m-d')
        ])->get();

        return response()->json([
            'status' => 200,
            'customers' => $customers,
            'thisMonth' => $thisMonth,
        ]);
    }
}
