<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use App\Exports\SalesExport;
use App\Exports\CustomerExport;

use App\Models\Visitor;
use App\Models\VisitorOrder;

class ExportController extends Controller
{
    public function customer(Request $request) {
        $token = $request->token;
        $now = Carbon::now();

        $user = UserController::get($token)->first();
        $name = ucwords($user->name);
        $name = str_replace(' ', '_', $name);

        $filename = $name."_Customer_Rerort_".$now->isoFormat('MMMM').".xlsx";

        $datas = Visitor::where('user_id', $user->id)
        ->whereBetween('created_at', [
            $now->startOfMonth()->format('Y-m-d'),
            $now->endOfMonth()->format('Y-m-d')
        ])->get();

        $storeFile = Excel::store(new CustomerExport([
            'datas' => $datas
        ]), $filename, 'export');

        return response()->json([
            'status' => 200,
            'link' => asset('storage/export/'.$filename)
        ]);
    }
    public function sales(Request $request) {
        $token = $request->token;
        $now = Carbon::now();

        $user = UserController::get($token)->first();
        $name = ucwords($user->name);
        $name = str_replace(' ', '_', $name);

        $filename = $name."_Sales_Rerort_".$now->isoFormat('MMMM')."_".$now->format('Y').".xlsx";

        $datas = VisitorOrder::where([
            ['is_placed', 1],
            ['user_id', $user->id]
        ])
        ->whereBetween('created_at', [
            $now->startOfMonth()->format('Y-m-d'),
            $now->endOfMonth()->format('Y-m-d')
        ])
        ->with(['visitor','details'])->get();

        $storeFile = Excel::store(new SalesExport([
            'datas' => $datas,
            'user' => $user,
            'period' => $now->isoFormat('MMMM'),
        ]), $filename, 'export');

        return response()->json([
            'status' => 200,
            'user' => $user,
            'period' => $now->isoFormat('MMMM'),
            'link' => asset('storage/export/'.$filename)
        ]);
    }
}
