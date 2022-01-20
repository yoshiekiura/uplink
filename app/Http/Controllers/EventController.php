<?php

namespace App\Http\Controllers;

use Storage;
use Validator;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function store(Request $request) {
        $token = $request->token;
        $user = UserController::get($token)->first();

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
            'price_sale' => $request->price_sale,
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
