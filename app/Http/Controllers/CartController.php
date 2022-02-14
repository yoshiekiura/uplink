<?php

namespace App\Http\Controllers;

use DB;
use Log;
use Str;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Event;
use App\Models\Visitor;
use App\Models\VisitorOrder;
use App\Models\VisitorOrderDetail;

class CartController extends Controller
{
    public static $classToCall = [
        'event' => [
            'name' => '\App\Models\Event'
        ],
        'digital_product' => [
            'name' => '\App\Models\DigitalProduct',
            'relation' => 'images',
            'price_column' => 'price',
        ],
        'support' => [
            'name' => '\App\Models\Support',
            'price_column' => 'price_unit',
        ]
    ];
    public function get(Request $request) {
        $token = $request->token;
        $visitor = Visitor::where('token', $token)->first();
        $cart = VisitorOrder::where([
            ['visitor_id', $visitor->id],
            ['user_id', $request->user_id],
            ['is_placed', 0]
        ])
        ->with('details')
        ->first();

        $details = $cart->details;
        
        foreach ($details as $item) {
            $productType = $item->product_type;
            $classModel = self::$classToCall[$productType];
            $className = $classModel['name'];
            $queryProduct = $className::where('id', $item->{$productType});
            if (array_key_exists('relation', $classModel)) {
                $queryProduct = $queryProduct->with($classModel['relation']);
            }
            $item->product = $queryProduct->first();
        }

        return response()->json([
            'status' => 200,
            'token' => $token,
            'message' => "Berhasil mengambil cart",
            'visitor' => $visitor,
            'cart' => $cart,
        ]);
    }
    public function store(Request $request) {
        $visitorToken = $request->visitor_token;
        $userID = $request->user_id;
        $itemType = $request->item_type;
        $itemID = $request->item_id;
        $visitor = Visitor::where('token', $visitorToken)->first();
        $user = User::find($userID);
        $quantity = $request->quantity == "" ? 1 : $request->quantity;
        $itemPrice = $request->item_price * $quantity;

        // check if has cart
        $cartQuery = VisitorOrder::where([
            ['visitor_id', $visitor->id],
            ['user_id', $user->id],
            ['is_placed', 0]
        ]);
        $checkIfHasCart = $cartQuery->first();

        if ($checkIfHasCart == "") {
            $msg = "belum ada cart";
            $invNumber = rand(111111, 999999);
            $saveCart = VisitorOrder::create([
                'visitor_id' => $visitor->id,
                'user_id' => $user->id,
                'invoice_number' => $invNumber,
                'total' => $request->item_price,
                'grand_total' => $request->item_price,
                'is_placed' => 0
            ]);

            $detailToSave['order_id'] = $saveCart->id;
            $detailToSave['product_type'] = $itemType;
            $detailToSave[$itemType] = $itemID;
            $detailToSave['quantity'] = $quantity;
            $detailToSave['total_price'] = $request->item_price;
            $saveDetail = VisitorOrderDetail::create($detailToSave);

            return response()->json([
                'status' => 200
            ]);
        } else {
            $cart = $checkIfHasCart;
            $data = VisitorOrderDetail::where([
                ['order_id', $cart->id],
                [$itemType, $itemID]
            ]);
            $item = $data->first();

            if ($item == "") {
                // belum ada item
                $msg = "belum ada item";
                $detailToSave['order_id'] = $cart->id;
                $detailToSave['product_type'] = $itemType;
                $detailToSave[$itemType] = $itemID;
                $detailToSave['quantity'] = $quantity;
                $detailToSave['total_price'] = $itemPrice;
                $saveDetail = VisitorOrderDetail::create($detailToSave);
                $newOrderPrice = $itemPrice + $cart->total;
            } else {
                // sudah ada item
                $msg = "sudah ada item";
                $newQuantity = $item->quantity + 1;
                $newPrice = $itemPrice * $newQuantity;
                $updateItem = $data->update([
                    'quantity' => DB::raw('quantity + 1'),
                    'total_price' => $newPrice
                ]);
                $newOrderPrice = ($cart->total - $itemPrice) + $newPrice;
            }

            $updateOrder = $cartQuery->update([
                'total' => $newOrderPrice,
                'grand_total' => $newOrderPrice
            ]);
        }

        return response()->json([
            'message' => $msg
        ]);
    }
    public function increase($itemID, $type) {
        $data = VisitorOrderDetail::where('id', $itemID);
        $productType = $data->first('product_type')->product_type;
        $productTypePrefix = $productType."_item";
        $item = $data->with($productTypePrefix)->first();
        $product = $item->{$productTypePrefix};
        $cartQuery = VisitorOrder::where('id', $item->order_id);
        $cart = $cartQuery->first();
        $priceCol = self::$classToCall[$productType]['price_column'];
        $productPrice = $product->{$priceCol};

        if ($type == "increase") {
            $newQuantity = $item->quantity + 1;
        } else if ($type == "delete") {
            $newQuantity = 0;
        } else {
            $newQuantity = $item->quantity - 1;
        }
        $newPrice = $productPrice * $newQuantity;
        if ($type == "increase") {
            $newOrderPrice = $cart->total - $productPrice + $newPrice;
        } else if ($type == "decrease") {
            $newOrderPrice = $cart->total - $productPrice;
        } else if ($type == "delete") {
            $newOrderPrice = $cart->total - $item->total_price;
        }

        if ($type == "delete") {
            $deleteItem = $data->delete();
        } else {
            $updateItem = $data->update([
                'quantity' => $newQuantity,
                'total_price' => $newPrice
            ]);
        }

        $updateCart = $cartQuery->update([
            'total' => $newOrderPrice,
            'grand_total' => $newOrderPrice,
        ]);

        return response()->json(['status' => 200]);
    }
    public function checkout($cartID) {
        $data = VisitorOrder::where('id', $cartID)->update([
            'is_placed' => 1,
            'note' => $request->note
        ]);
        return response()->json(['status' => 200]);
    }
}
