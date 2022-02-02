<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('tes', 'OtpController@tes');

Route::get('/', function () {
    // return bcrypt('inikatasandi');
    $url = "https://vt.tiktok.com/ZSeVVEyPD/";
    $page = file_get_contents($url);
    // $title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $page, $match) ? $match[1] : null;
    echo "<pre>";
    echo $page;
});

Route::get('{username}', "WebController@LandingPage");
Route::get('{username}/checkout', "WebController@CheckoutPage");

Route::group(['prefix' => "admin"], function () {
    Route::get('login', "AdminController@loginPage")->name('admin.loginPage');
    Route::post('login', "AdminController@login")->name('admin.login');
    Route::get('logout', "AdminController@logout")->name('admin.logout');
    
    Route::get('dashboard', "AdminController@dashboard")->name('admin.dashboard')->middleware('Admin');

    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
});
