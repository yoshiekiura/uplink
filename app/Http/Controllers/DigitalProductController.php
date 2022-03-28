<?php

namespace App\Http\Controllers;

use Storage;
use App\Models\DigitalProduct;
use App\Models\DigitalProductImage;
use Illuminate\Http\Request;

class DigitalProductController extends Controller
{
    public function get(Request $request, $categoryID = null) {
        $token = $request->token;
        if ($categoryID == null) {
            $user = UserController::get($token)->first();
            $products = DigitalProduct::where('user_id', $user->id)->with('images')->get();
        } else {
            $products = DigitalProduct::where('category_id', $categoryID)->with('images')->get();
        }

        return response()->json([
            'data' => $products,
            'status' => 200,
            'message' => "Berhasil mengambil produk digital"
        ]);
    }
    public function store(Request $request) {
        $token = $request->token;
        $user = UserController::get($token)->first();
        
        $saveData = DigitalProduct::create([
            'category_id' => $request->category_id,
            'user_id' => $user->id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'platform' => $request->platform,
            'url' => $request->url,
            'custom_message' => $request->custom_message
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageFileName = time()."_".$saveData->id."_".$image->getClientOriginalName();
                $saveImage = DigitalProductImage::create([
                    'product_id' => $saveData->id,
                    'filename' => $imageFileName,
                    'priority' => 0
                ]);
                $image->storeAs('public/digital_product_images', $imageFileName);
            }
        }

        return response()->json([
            'status' => 200,
            'message' => "Berhasil menambahkan produk digital",
            'data' => $saveData
        ]);
    }
    public function delete(Request $request) {
        $data = DigitalProduct::where('id', $request->id)->with('images');
        $product = $data->first();
        $deleteData = $data->delete();

        foreach ($product->images as $image) {
            $deleteImage = Storage::delete('public/digital_product_images'.$image->filename);
        }
        
        return response()->json([
            'status' => 200,
            'message' => "Berhasil menghapus produk ".$product->name
        ]);
    }
    public function getByID($id, $directReturn = false) {
        $data = DigitalProduct::where('id', $id)->with('images')->first();
        if ($directReturn) {
            return $data;
        }

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }
    public function removeImage($imageID) {
        $data = DigitalProductImage::where('id', $imageID);
        $image = $data->first();
        $deleteData = $data->delete();
        $deleteFile = Storage::delete('public/digital_product_images/' . $image->filename);
        return response()->json(['status' => 200]);
    }
}
