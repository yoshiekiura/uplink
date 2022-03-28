<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Support;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function get(Request $request) {
        $token = $request->token;
        $user = UserController::get($token)->with('supports')->first();
        $datas = $user->supports;
        unset($user->supports);

        return response()->json([
            'status' => 200,
            'message' => "Berhasil mengambil data support",
            'datas' => $datas,
            'user' => $user
        ]);
    }
    public function getByUserID($userID) {
        $datas = Support::where('user_id', $userID)->get();
        return response()->json([
            'status' => 200,
            'message' => "Berhasil mengambil data support",
            'datas' => $datas
        ]);
    }
    public function getByID($itemID, $directReturn = false) {
        $data = Support::where('id', $itemID)->first();
        if ($directReturn) {
            return $data;
        }
        
        return response()->json([
            'status' => 200,
            'message' => "Berhasil mengambil data support",
            'support' => $data
        ]);
    }
    public function store(Request $request) {
        $customMessagesValidator = ['required' => ":attribute harus diisi",];
        $validateData = Validator::make($request->all(), [
            'stuff' => 'required',
            'button_text' => 'required',
            'description' => 'required',
        ], $customMessagesValidator);
        if ($validateData->fails()) {
            return response()->json(['status' => 500, 'data' => $validateData->messages()]);
        }

        $user = UserController::get($request->token)->first();

        $toSave = [
            'user_id' => $user->id,
            'stuff' => $request->stuff,
            'button_text' => $request->button_text,
            'description' => $request->description,
        ];

        if ($request->price_unit != "") {
            $toSave['price_unit'] = $request->price_unit;
        }
        if ($request->custom_message != "") {
            $toSave['custom_message'] = $request->custom_message;
        }
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageFileName = $image->getClientOriginalName();
            $image->storeAs('public/support_image', $imageFileName);
            $toSave['image'] = $imageFileName;
        }

        $saveData = Support::create($toSave);

        return response()->json([
            'status' => 200,
            'message' => "Berhasil menambahkan item support",
            'data' => $saveData
        ]);
    }
    public function delete(Request $request) {
        $id = $request->id;
        if ($id == "") {
            return response()->json(['status' => 500, 'message' => 'ID harus disertakan']);
        }

        $data = Support::where('id', $id);
        $support = $data->first();
        $deleteData = $data->delete();
        if ($support->image != null) {
            $deleteImage = Storage::delete('public/support_image/'.$support->image);
        }

        return response()->json([
            'status' => 200,
            'message' => "Berhasil menghapus item support"
        ]);
    }
    public function update($id, Request $request) {
        $data = Support::where('id', $id);
        $support = $data->first();
        $updateData = $data->update([
            'stuff' => $request->stuff,
            'price_unit' => $request->price_unit,
            'description' => $request->description,
            'custom_message' => $request->custom_message,
            'button_text' => $request->button_text,
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Berhasil mengubah data support"
        ]);
    }
}
