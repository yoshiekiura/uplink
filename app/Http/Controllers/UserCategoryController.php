<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\UserCategory;
use Illuminate\Http\Request;

class UserCategoryController extends Controller
{
    public function get(Request $request) {
        $token = $request->token;
        $with = $request->with;
        $relation = 'user_categories';
        if ($with != "") {
            $relation .= ".".$with;
        }

        $availableRelation = ['links','events'];
        if (!in_array($with, $availableRelation)) {
            return response()->json([
                'status' => 501,
                'message' => "Opsi data tidak tersedia"
            ]);
        }

        $user = UserController::get($token)->with($relation)->first();
        $categories = $user->user_categories;
        unset($user->user_categories);

		$i = 0;
		foreach ($categories as $category) {
			$iPP = $i++;
			$categories[$iPP]->image = asset('storage/user_category_images/'.$category->image);
		}

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
        $customMessagesValidator = [
            'required' => "Bidang :attribute harus diisi",
            'image' => "Bidang :attribute harus berupa gambar"
        ];
        $validateData = Validator::make($request->all(), ['name' => 'required','token' => 'required','image' => 'image'], $customMessagesValidator);
        if ($validateData->fails()) {
            return response()->json(['status' => 500, 'data' => $validateData->messages()]);
        }

        $user = UserController::get($request->token)->first();
        $toSave = [
            'user_id' => $user->id,
            'name' => $request->name,
            'has_used' => 0
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageFileName = time()."_".$image->getClientOriginalName();
            $toSave['image'] = $imageFileName;
            $image->storeAs('public/user_category_images', $imageFileName);
        }
        
        $saveData = UserCategory::create($toSave);

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
