<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\SocialLink;
use Illuminate\Http\Request;

class SocialLinkController extends Controller
{
    public function get(Request $request) {
        $user = UserController::get($request->token)->with('socials')->first();
        $socials = $user->socials;
        unset($user->socials);
        return response()->json(['status' => 200, 'user' => $user, 'socials' => $socials]);
    }
    public function store(Request $request) {
        $customMessagesValidator = ['required' => ":attribute harus diisi",];
        $validateData = Validator::make($request->all(), [
            'type' => 'required',
            'token' => 'required',
            'url' => 'required',
        ], $customMessagesValidator);
        if ($validateData->fails()) {
            return response()->json(['status' => 500, 'data' => $validateData->messages()]);
        }

        $user = UserController::get($request->token)->first();

        $saveData = SocialLink::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'url' => $request->url,
        ]);
        
        return response()->json(['status' => 200, 'message' => "Berhasil menambahkan social link"]);
    }
    public function update(Request $request) {
        $id = $request->id;
        $data = SocialLink::where('id', $id);
        if ($data->first() == "") {
            return response()->json(['status' => 404, 'message' => "Link tidak ditemukan"]);
        }

        $customMessagesValidator = ['required' => ":attribute harus diisi",];
        $validateData = Validator::make($request->all(), ['url' => 'required',], $customMessagesValidator);
        if ($validateData->fails()) {
            return response()->json(['status' => 500, 'data' => $validateData->messages()]);
        }
        
        $updateData = $data->update([
            'type' => $request->type,
            'url' => $request->url,
        ]);
        return response()->json(['status' => 200, 'message' => "Berhasil mengubah social link"]);
    }
    public function delete(Request $request) {
        $id = $request->id;
        $data = SocialLink::where('id', $id);
        if ($data->first() == "") {
            return response()->json(['status' => 404, 'message' => "Link tidak ditemukan"]);
        }
        $deleteData = $data->delete();
        return response()->json(['status' => 200, 'message' => "Berhasil menghapus social link"]);
    }
}
