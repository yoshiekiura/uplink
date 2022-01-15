<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\UserCategory;
use Illuminate\Http\Request;

class UserCategoryController extends Controller
{
    public function get(Request $request) {
        $token = $request->token;
        $user = UserController::get($token)->with('data_categories')->first();
        $categories = $user->data_categories;
        unset($user->data_categories);

        return response()->json([
            'status' => 200,
            'message' => "Data kategori berhasil diambil",
            'data' => [
                'user' => $user,
                'categories' => $categories,
            ]
        ]);
    }
    public function store(Request $request) {
        $customMessagesValidator = ['required' => ":attribute harus diisi",];
        $validateData = Validator::make($request->all(), ['name' => 'required','token' => 'required'], $customMessagesValidator);
        if ($validateData->fails()) {
            return response()->json(['status' => 500, 'data' => $validateData->messages()]);
        }

        $user = UserController::get($request->token)->first();
        
        $saveData = UserCategory::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'has_used' => 0
        ]);

        return response()->json(['status' => 200, 'message' => "Berhasil menambahkan kategori"]);
    }
    public function update(Request $request) {
        $customMessagesValidator = ['required' => ":attribute harus diisi",];
        $validateData = Validator::make($request->all(), [
            'name' => 'required',
            'token' => 'required',
            'id' => 'required'
        ], $customMessagesValidator);
        if ($validateData->fails()) {
            return response()->json(['status' => 500, 'data' => $validateData->messages()]);
        }

        $id = $request->id;
        $data = UserCategory::where('id', $id);
        if ($data->first() == "") {
            return response()->json(['status' => 404, 'message' => "Kategori tidak ditemukan"]);
        }
        $updateData = $data->update([
            'name' => $request->name,
        ]);

        return response()->json(['status' => 200, 'message' => "Berhasil mengubah kategori"]);
    }
    public function delete(Request $request) {
        $id = $request->id;
        $data = UserCategory::where('id', $id);
        if ($data->first() == "") {
            return response()->json(['status' => 404, 'message' => "Kategori tidak ditemukan"]);
        }
        $deleteData = $data->delete();
        return response()->json(['status' => 200, 'message' => "Berhasil menghapus kategori"]);
    }
}
