<?php

namespace App\Http\Controllers;

use Log;
use Storage;
use App\Models\Product;
use App\Models\ProductImage as Image;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public static function isMine() {
        // 
    }
    public function store(Request $request) {
        $token = $request->token;
        $user = UserController::get($token)->first();

        $saveData = Product::create([
            'user_id' => $user->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'weight' => $request->weight,
            'dimension' => $request->dimension,
            'stock' => $request->stock,
        ]);

        foreach ($request->file('images') as $image) {
            $imageFileName = time()."_".$saveData->id."_".$image->getClientOriginalName();
            $saveImage = Image::create([
                'product_id' => $saveData->id,
                'filename' => $imageFileName,
                'priority' => 0
            ]);
            $image->storeAs('public/product_images', $imageFileName);
        }

        return response()->json([
            'status' => 200,
            'message' => "Berhasil menambahkan produk"
        ]);
    }
    public function delete($id, Request $request) {
        $user = UserController::get($request->token)->first();
        $data = Product::where('id', $id);
        $product = $data->with('images')->first();

        $res['status'] = 200;
        $res['message'] = "Berhasil menghapus produk";

        if ($product->user_id != $user->id) {
            $res['status'] = 403;
            $res['message'] = "Anda tidak dapat menghapus produk ini";
        } else {
            if ($product != "") {
                $deleteData = $data->delete();
                foreach ($product->images as $image) {
                    $deleteImage = Storage::delete('public/product_images/'.$image->filename);
                }
            } else {
                $res['status'] = 404;
                $res['message'] = "Produk tidak ditemukan";
            }
        }

        return response()->json($res);
    }
    public function update($id, Request $request) {
        $user = UserController::get($request->token)->first();
        $data = Product::where('id', $id);
        $product = $data->first();

        $res['status'] = 200;
        $res['message'] = "Berhasil menghapus produk";

        if ($product->user_id != $user->id) {
            $res['status'] = 403;
            $res['message'] = "Anda tidak dapat menghapus produk ini";
        } else {
            if ($product != "") {
                $toUpdate = [
                    'name' => $request->name,
                    'description' => $request->description,
                    'price' => $request->price,
                    'stock' => $request->stock,
                    'weight' => $request->weight,
                ];

                $updateData = $data->update($toUpdate);
            } else {
                $res['status'] = 404;
                $res['message'] = "Produk tidak ditemukan";
            }
        }

        return response()->json($res);
    }
}
