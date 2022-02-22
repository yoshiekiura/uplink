<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Models\VisitorOrder;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function get(Request $request) {
        $token = $request->token;
        $myData = UserController::get($token)->first();

        $getSales = VisitorOrder::where([
            ['user_id', $myData->id],
            ['is_placed', 1]
        ])
        ->with(['visitor','details'])
        ->get();

        $productCount = 0;
        foreach ($getSales as $item) {
            $productCount += $item->details->sum('quantity');
        }
        $revenue = $getSales->sum('grand_total');

        return response()->json([
            'sales' => $getSales,
            'product' => $productCount,
            'revenue' => $revenue
        ]);
    }
    public function detail($id, Request $request) {
        $data = VisitorOrder::where('id', $id)
        ->with(['voucher','visitor','details'])
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

        return response()->json([
            'sales' => $data,
        ]);
    }
}
