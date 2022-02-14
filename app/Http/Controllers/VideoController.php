<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function get(Request $request) {
        $user = UserController::get($request->token)->first();
        $videos = Video::where('user_id', $user->id)
        ->orderBy('priority', 'DESC')->orderBy('updated_at', 'DESC')
        ->get();

        return response()->json([
            'status' => 200,
            'message' => "Video berhasil diambil",
            'data' => $videos
        ]);
    }
    public function getByID($id) {
        $video = Video::where('id', $id)->with('user')->first();
        if ($video == "") {
            return response()->json([
                'status' => 200,
                'message' => "Video tidak ditemukan"
            ]);
        }
        
        return response()->json([
            'status' => 200,
            'message' => "Video berhasil diambil",
            'data' => $video
        ]);
    }
    public function getByUserID($userID) {
        $datas = Video::where('user_id', $userID)->get();
        return response()->json([
            'status' => 200,
            'datas' => $datas
        ]);
    }
    public static function getTitle($url) {
        $page = file_get_contents($url);
        $title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $page, $match) ? $match[1] : null;
        return $title;
    }
    public function store(Request $request) {
        $user = UserController::get($request->token)->first();

        $url = $request->url;
        if (strpos($url, "youtu") !== false) {
            $type = "youtube";
            $title = self::getTitle($url);
            $title = explode(" - Youtube", $title)[0];
        } else if (strpos($url, "tiktok") !== false) {
            $type = "tiktok";
            $title = "TikTok Video";
        } else {
            return response()->json([
                'status' => 501,
				'url' => $url,
                'message' => "URL bukan dari Youtube maupun TikTok"
            ]);
        }

        $saveData = Video::create([
            'user_id' => $user->id,
            'url' => $url,
            'title' => $title,
            'type' => $type,
            'priority' => 0,
            'play_count' => 0
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Berhasil menambahkan video",
            'data' => $saveData
        ]);
    }
    public function delete(Request $request) {
        $id = $request->id;
        $data = Video::where('id', $id);
        $deleteVideo = $data->delete();
        
        return response()->json([
            'status' => 200,
            'message' => "Berhasil menghapus video"
        ]);
    }
    public function priority(Request $request) {
        $id = $request->id;
        $data = Video::where('id', $id);
        if ($request->type == "increase") {
            $data->increment('priority');
        } else {
            $data->decrement('priority');
        }

        return response()->json([
            'status' => 200,
            'message' => "Berhasil mengubah priority video"
        ]);
    }
}
