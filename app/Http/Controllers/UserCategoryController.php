<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Validator;
use App\Models\User;
use App\Models\UserCategory;
use Illuminate\Http\Request;

class UserCategoryController extends Controller
{
    public function get(Request $request, $id = null) {
        $categories = "";
        $availableRelation = ['links','events','digital_products','digital_products.images'];
        $with = $request->with;
        $token = $request->token;
        
        if ($id == null) {
            if ($with != "") {
                if (!in_array($with, $availableRelation)) {
                    return response()->json([
                        'status' => 501,
                        'message' => "Opsi data tidak tersedia"
                    ]);
                }
            }
    
            if ($token != "") {
                $relation = "user_categories";
                if ($with != "") {
                    $relation .= ".".$with;
                }
                $user = User::where('token', $token)->with($relation)->first();
                $categories = $user->user_categories;
            } else if ($request->user_id != "") {
                $categoryQuery = UserCategory::where('user_id', $request->user_id);
                if ($with != "") {
                    $categoryQuery = $categoryQuery->with($with);
                }
                $categories = $categoryQuery->get();
            }
        } else {
            $query = UserCategory::where('id', $id);
            if ($with != "") {
                $query = $query->with($with);
            }
            $categories = $query->get();
        }

		if ($categories != "" && count($categories) > 0) {
            $i = 0;
            foreach ($categories as $category) {
                $iPP = $i++;
                $categories[$iPP]->image = asset('storage/user_category_images/'.$category->image);
            }
        }

        return response()->json([
            'status' => 200,
            'token' => $token,
            'message' => "Data kategori berhasil diambil",
            'data' => [
                'categories' => $categories,
            ]
        ]);
    }
    public function getItems($categoryID, $type) {
        $category = UserCategory::where('id', $categoryID)->with($type)->first();
        $items = $category->{$type};
        unset($category->{$type});

        return response()->json([
            'category' => $category,
            $type => $items
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
