<?php

namespace App\Http\Controllers;

use Storage;
use Validator;
use App\Models\Link;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public static function authenticate() {
        // 
    }
    public function get(Request $request) {
        $token = $request->token;
        $user = UserController::get($token)->first();
        $links = Link::where('user_id', $user->id)->get();
    }
    public function store(Request $request) {
        $customMessagesValidator = ['required' => ":attribute harus diisi",];
        $validateData = Validator::make($request->all(), [
            'title' => 'required',
            'url' => 'required',
            'category_id' => 'required',
        ], $customMessagesValidator);
        if ($validateData->fails()) {
            return response()->json(['status' => 500, 'data' => $validateData->messages()]);
        }

        $user = UserController::get($request->token)->first();
        $meta = get_meta_tags($request->url);
        $toSave = [
            'user_id' => $user->id,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $meta['description'],
            'url' => $request->url,
            'priority' => 0
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageFileName = $image->getClientOriginalName();
            $toSave['image'] = $imageFileName;
            $image->storeAs('public/link_image', $imageFileName);
        }

        $saveData = Link::create($toSave);

        return response()->json([
            'status' => 200,
            'message' => "Berhasil menambahkan link",
            'data' => $saveData
        ]);
    }
    public function delete(Request $request) {
        $id = $request->id;
        $data = Link::where('id', $id);
        $link = $data->first();
        if ($link == "") {
            return response()->json([
                'status' => 404,
                'message' => "Tidak dapat menemukan link dengan ID ".$id
            ]);
        }
        $user = UserController::get($request->token)->first();

        if ($user->id != $link->user_id) {
            return response()->json(['status' => 400, 'Anda tidak dapat mengakses data ini']);
        }

        $deleteData = $data->delete();
        if ($link->image != null) {
            $deleteImage = Storage::delete('public/link_image/'.$link->image);
        }

        return response()->json(['status' => 200, 'message' => 'Link berhasil dihapus']);
    }
}
