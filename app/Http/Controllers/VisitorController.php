<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Visitor;
use App\Models\Link;
use App\Models\LinkStat;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function register(Request $request) {
        $token = Str::random(32);
        $saveData = Visitor::create([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->json($saveData);
    }
    public function visitLink($id) {
        $today = Carbon::now()->format('Y-m-d');
        $data = LinkStat::where([
            ['link_id', $id],
            ['date', $today]
        ])->with('link');

        $stat = $data->first();
        if ($stat == "") {
            $createData = LinkStat::create([
                'link_id' => $id,
                'date' => $today,
                'count' => 1
            ]);
            $link = Link::where('id', $id)->first();
        } else {
            $data->increment('count');
            $link = $stat->link;
        }

        echo "Redirecting to ".$link->url." ...";

        return redirect($link->url);
    }
}
