<?php

namespace App\Http\Controllers;

use Str;
use Carbon\Carbon;
use App\Models\Visitor;
use App\Models\VisitorOrder;
use App\Models\VisitorOrderDetail;
use App\Models\Link;
use App\Models\LinkStat;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
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
        $visitor = Visitor::where('token', $token)->with('transactions')
        ->whereHas('transactions', function ($query) {
            $query->where('is_placed', 1);
        })
        ->first();

        return response()->json([
            'status' => 200,
            'visitor' => $visitor
        ]);
    }
    public function transactionDetail($id, Request $request) {
        $data = VisitorOrder::where('id', $id)
        ->with('details')
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
}
