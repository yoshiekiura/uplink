<?php

namespace App\Http\Controllers;

use Log;
use Storage;
use Validator;
use Carbon\Carbon;
use App\Models\Link;
use App\Models\LinkStat;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public static function authenticate() {
        // 
    }
    public function get(Request $request, $categoryID = null) {
        $token = $request->token;
        if ($categoryID == null) {
            $user = UserController::get($token)->first();
            $links = Link::where('user_id', $user->id)->get();
        } else {
            $links = Link::where('category_id', $categoryID)->get();
        }
        return response()->json([
            'status' => 200,
            'message' => "Data link berhasil diambil",
            'data' => $links
        ]);
    }
    public function getByID($linkID, Request $request) {
        $token = $request->token;
        $link = Link::where('id', $linkID)->first();
        return response()->json([
            'status' => 200,
            'message' => "Berhasil mengambil data link",
            'link' => $link
        ]);
    }
    public function store(Request $request) {
        $customMessagesValidator = ['required' => ":attribute harus diisi",];
        $validateData = Validator::make($request->all(), [
            'title' => 'required',
            'url' => 'required',
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
    public function update(Request $request) {
        $id = $request->id;
        $data = Link::where('id', $id);
        $description = "No info available";
        
        $meta = get_meta_tags($request->url);
        if ($meta) {
            $description = $meta['description'];
        }

        $updateData = $data->update([
            'title' => $request->title,
            'url' => $request->url,
            'description' => $description
        ]);

        return response()->json(['status' => 200, 'message' => 'Berhasil mengubah link']);
    }
    public function statisticByID($id, $filter = "month") {
        $now = Carbon::now();
        $data = Link::where('id', $id);
        $toReturn = [];

        if ($filter == "month") {
            $datas = LinkStat::where([
                ['link_id', $id],
                ['date', "LIKE", "%".$now->format('Y-m')."%"]
            ])
            ->orderBy('date', 'ASC')->get();

            foreach ($datas as $i => $data) {
                if (count($toReturn) == 0) {
                    array_push($toReturn, [
                        'date' => $data->date,
                        'click' => $data->count
                    ]);
                } else {
                    $isDataFound = false;
                    foreach ($toReturn as $k => $ret) {
                        if ($ret['date'] == $data->date) {
                            $isDataFound = $k;
                        }
                    }

                    if ($isDataFound === false) {
                        array_push($toReturn, [
                            'date' => $data->date,
                            'click' => $data->count
                        ]);
                    } else {
                        $toReturn[$isDataFound]['click'] += $data->count;
                    }
                }
            }
        } else {
            $monthCount = 6;
            if ($filter == "semester") {
                $monthCount = 6;
            }else if ($filter == "yearly") {
                $monthCount = 12;
            }
            $startDate = $now->subMonths($monthCount)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');

            $datas = LinkStat::where('link_id', $id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'ASC')->get();

            foreach ($datas as $i => $data) {
                $month = self::formatDate($data->date, 'MMM');
                if (count($toReturn) == 0) {
                    array_push($toReturn, [
                        'month' => $month,
                        'click' => $data->count
                    ]);
                } else {
                    $isDataFound = false;
                    foreach ($toReturn as $k => $ret) {
                        if ($ret['month'] == $month) {
                            $isDataFound = $k;
                        }
                    }
                    if ($isDataFound === false) {
                        array_push($toReturn, [
                            'month' => $month,
                            'click' => $data->count
                        ]);
                    } else {
                        $toReturn[$isDataFound]['click'] += $data->count;
                    }
                }
            }
        }

        return response()->json([
            'status' => 200,
            'datas' => $toReturn,
        ]);
    }
    public static function formatDate($date, $form) {
        return Carbon::parse($date)->isoFormat($form);
    }
    public function statistic(Request $request) {
        $now = Carbon::now();
        $startDate = $now->startOfMonth()->format('Y-m-d');
        $endDate = $now->endOfMonth()->format('Y-m-d');
        $token = $request->token;
        $user = UserController::get($token)->first();

        $rawDatas = LinkStat::whereBetween('date', [$startDate, $endDate])
        ->whereHas('link', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with('link')->get();

        $storedLinkID = [];
        $links = [];

        foreach ($rawDatas as $data) {
            if (in_array($data->link_id, $storedLinkID)) {
                // sudah ada
                foreach ($links as $l => $link) {
                    if ($link['id'] == $data->link_id) {
                        $links[$l]['count'] += $data->count;
                    }
                }
            } else {
                // belum ada
                if (count($storedLinkID) <= 10) {
                    array_push($links, [
                        'id' => $data->link->id,
                        'user_id' => $data->link->user_id,
                        'category_id' => $data->link->category_id,
                        'title' => $data->link->title,
                        'url' => $data->link->url,
                        'description' => $data->link->description,
                        'count' => $data->count
                    ]);
                    array_push($storedLinkID, $data->link_id);
                }
            }
        }

        return response()->json([
            'status' => 200,
            'token' => $token,
            'links' => $links
        ]);
    }
}
