<?php

namespace App\Http\Controllers;

use Storage;
use Validator;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function get(Request $request, $categoryID = null) {
        $token = $request->token;
    }
    public function getByID($id) {
        $event = Event::where('id', $id)->first();
        return response()->json([
            'status' => 200,
            'message' => "Berhasil mengambil data event",
            'data' => $event
        ]);
    }
    public function store(Request $request) {
        $token = $request->token;
        $user = UserController::get($token)->first();
        $priceSale = $request->price_sale == "null" ? null : $request->price_sale;

        $quantity = $request->quantity == "" ? -1 : $request->quantity;
        $cover = $request->file('cover');
        $coverFileName = $cover->getClientOriginalName();

        $saveData = Event::create([
            'user_id' => $user->id,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'cover' => $coverFileName,
            'platform' => $request->platform,
            'platform_url' => $request->platform_url,
            'date' => $request->date,
            'duration' => $request->duration,
            'price' => $request->price,
            'price_sale' => $priceSale,
            'quantity' => $quantity,
            'custom_message' => $request->custom_message,
            'action_button_text' => $request->action_button_text,
        ]);

        $cover->storeAs('public/event_cover', $coverFileName);

        return response()->json([
            'status' => 200,
            'message' => "Event berhail dibuat"
        ]);
    }
    public function delete(Request $request) {
        $id = $request->id;
        $data = Event::where('id', $id);
        $event = $data->first();
        $deleteData = $data->delete();
        $deleteCover = Storage::delete('public/event_cover/'.$event->cover);

        return response()->json([
            'status' => 200,
            'message' => "Berhasil menghapus event ".$event->title
        ]);
    }
}
