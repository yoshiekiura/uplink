<?php

namespace App\Http\Controllers;

use Http;
use Illuminate\Http\Request;

class RajaongkirController extends Controller
{
    public $baseUrl = null;
    public $apiKey = null;

    public static function getBaseUrl() {
        $accountType = strtolower(env('RAJAONGKIR_ACCOUNT_TYPE'));
        if ($accountType == "starter") {
            return "https://api.rajaongkir.com/starter";
        } else if ($accountType == "basic") {
            return "https://api.rajaongkir.com/basic";
        } else {
            return "https://pro.rajaongkir.com/api";
        }
    }
    public function __construct() {
        $this->baseUrl = self::getBaseUrl();
        $this->apiKey = env('RAJAONGKIR_KEY');
    }
    public function province() {
        $uri = $this->baseUrl."/province//";
        $response = Http::withHeaders([
            'key' => $this->apiKey
        ])->get($uri);
        $body = $response->body();
        $body = json_decode($body, false);

        return response()->json($body->rajaongkir->results);
    }
    public function city($provinceID = null, $cityID = null) {
        if ($provinceID == null) {
            return response()->json([
                'status' => 501,
                'message' => "Harus menyertakan ID provinsi"
            ]);
        }
        $uri = $this->baseUrl."/city";
        $query = ['province' => $provinceID];
        if ($cityID != null) {
            $query['id'] = $cityID;
        }
        $response = Http::withHeaders([
            'key' => $this->apiKey
        ])->get($uri, $query);
        $body = json_decode($response->body(), false);

        return response()->json($body->rajaongkir->results);
    }
    public function cost(Request $request) {
        $origin = $request->origin;
        $destination = $request->destination;
        $weight = $request->weight;
        $courier = $request->courier;

        $uri = $this->baseUrl."/cost";
        $response = Http::withHeaders([
            'key' => $this->apiKey
        ])->post($uri, [
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $courier,
        ]);
        $body = json_decode($response->body(), false);

        return response()->json($body->rajaongkir->results);
    }
    public function courier() {
        $accountType = strtolower(env('RAJAONGKIR_ACCOUNT_TYPE'));
        if ($accountType == "starter") {
            $datas = [
                ['code' => "jne",'name' => "JNE"],
                ['code' => "pos",'name' => "POS Indonesia"],
                ['code' => "tiki",'name' => "TIKI"],
            ];
        } else if ($accountType == "basic") {
            $datas = [
                ['code' => "jne",'name' => "JNE"],
                ['code' => "pos",'name' => "POS Indonesia"],
                ['code' => "tiki",'name' => "TIKI"],
            ];
        } else {
            $datas = [
                ['code' => "jne",'name' => "JNE"],
                ['code' => "pos",'name' => "POS Indonesia"],
                ['code' => "tiki",'name' => "TIKI"],
                ['code' => "sicepat",'name' => "SiCepat Express"],
                ['code' => "anteraja",'name' => "AnterAja"],
                ['code' => "lion",'name' => "Lion Parcel"],
                ['code' => "ninja",'name' => "Ninja Xpress"],
                ['code' => "j&t",'name' => "J&T"],
                ['code' => "jet",'name' => "JET Express"],
            ];
        }
        return response()->json($datas);
    }
}
