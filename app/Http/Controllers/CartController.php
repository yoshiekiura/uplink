<?php

namespace App\Http\Controllers;

use DB;
use Log;
use Str;
use Xendit\Xendit as Xendit;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Event;
use App\Models\Visitor;
use App\Models\UserVoucher;
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
        $details = null;
        $token = $request->token;
        $visitor = Visitor::where('token', $token)->first();
        $cart = VisitorOrder::where([
            ['visitor_id', $visitor->id],
            ['user_id', $request->user_id],
            ['is_placed', 0]
        ])
        ->with(['details','voucher'])
        ->first();

        if ($cart != "") {
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
                'is_placed' => 0,
                'has_withdrawn' => 0
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
    public function checkout($cartID, Request $request) {
        $data = VisitorOrder::where('id', $cartID);
        $cart = $data->first();

        $placeOrder = $data->update([
            'is_placed' => 1,
            'notes' => $request->note
        ]);
        $updateVoucher = UserVoucher::where('id', $cart->voucher_id)->decrement('quantity');

        return response()->json(['status' => 200]);
    }
    public function pay($cartID, Request $request) {
        $cartQuery = VisitorOrder::where('id', $cartID);
        $cart = $cartQuery->first();
        $referenceID = "UPLNK_".$cart->invoice_number;
        $externalID = 'uplink-'.$cart->invoice_number;

        $channelCode = $request->channel;
        $paymentMethod = $request->payment_method;

        Xendit::setApiKey(env('XENDIT_SECRET_KEY'));
        if ($paymentMethod == 'ewallets') {
            $channelName = explode("id_", $channelCode)[1];
            $args = [
                'reference_id' => $referenceID,
                'currency' => "IDR",
                'amount' => 1000,
                // 'amount' => $cart->grand_total,
                'checkout_method' => "ONE_TIME_PAYMENT",
                'channel_code' => strtoupper($channelCode),
                'channel_properties' => [
                    'success_redirect_url' => env('APP_URL')
                ]
            ];
            if ($channelCode == 'id_ovo') {
                $mobileNumber = preg_replace('/^0/', "+62", $request->mobile);
                $args['channel_properties']['mobile_number'] = $mobileNumber;
            }
            $makePayment = \Xendit\EWallets::createEWalletCharge($args);
        } else if ($paymentMethod == 'virtual_account') {
            $channelName = explode("fva_", $channelCode)[1];
            $args = [
                'external_id' => $externalID,
                'bank_code' => strtoupper($channelName),
                'name' => "Riyan dari Uplink",
                'is_single_use' => true,
                'expected_amount' => $cart->grand_total,
                'suggested_amount' => $cart->grand_total,
            ];
            $makePayment = \Xendit\VirtualAccounts::create($args);
        }

        $toUpdate = [
            'payment_reference_id' => $referenceID,
            'payment_external_id' => $externalID,
            'payment_method' => $channelName
        ];
        if ($paymentMethod == 'virtual_account') {
            $toUpdate['payment_owner_id'] = $makePayment['owner_id'];
            $toUpdate['payment_id'] = $makePayment['id'];
        }

        $updateCart = $cartQuery->update($toUpdate);

        return response()->json([
            'status' => 200,
            'payment' => $makePayment
        ]);
    }
    public function paymentStatus($id, Request $request) {
        $cartQuery = VisitorOrder::where('id', $id);
        $cart = $cartQuery->first();

        Xendit::setApiKey(env('XENDIT_SECRET_KEY'));
        if ($cart->payment_id != null) {
            $payment = \Xendit\VirtualAccounts::retrieve($cart->payment_id);
        }

        return response()->json([
            'status' => 200,
            'cart' => $cart,
            'payment' => $payment,
        ]);
    }
}
