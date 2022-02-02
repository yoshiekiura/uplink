<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Support;
use Illuminate\Http\Request;

class SupportController extends Controller
{
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
}