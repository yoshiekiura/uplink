<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => "user"], function () {
    Route::post('login', "UserController@login")->name("user.login");
    Route::post('register', "UserController@register")->name("user.register");
    Route::post('register-completion', "UserController@registerCompletion")->name("user.register.completion");
    Route::post('logout', "UserController@logout")->name("user.logout")->middleware('User');

    Route::post('me', "UserController@me")->name("user.me")->middleware('User');
});

Route::group(['prefix' => "social"], function () {
    Route::post('store', "SocialLinkController@store")->middleware('User');
    Route::post('delete', "SocialLinkController@delete")->middleware('User');
    Route::post('update', "SocialLinkController@update")->middleware('User');
    Route::post('/', "SocialLinkController@get")->middleware('User');
});

Route::group(['prefix' => "category"], function () {
    Route::post('store', "UserCategoryController@store")->middleware('User');
    Route::post('update', "UserCategoryController@update")->middleware('User');
    Route::post('delete', "UserCategoryController@delete")->middleware('User');
    Route::post('/', "UserCategoryController@get")->middleware('User');
});

Route::group(['prefix' => "link"], function () {
    Route::post('store', "LinkController@store")->middleware('User');
    Route::post('update', "LinkController@update")->middleware('User');
    Route::post('delete', "LinkController@delete")->middleware('User');
    Route::post('/', "LinkController@get")->middleware('User');
});

Route::group(['prefix' => "support"], function () {
    Route::post('store', "SupportController@store")->middleware('User');
    Route::post('update', "SupportController@update")->middleware('User');
    Route::post('delete', "SupportController@delete")->middleware('User');
    Route::post('/', "SupportController@get")->middleware('User');
});

Route::group(['prefix' => "visitor"], function () {
    Route::post('register', "VisitorController@register");
});

Route::group(['prefix' => "video"], function () {
    Route::post('store', "VideoController@store")->middleware('User');
    Route::post('update', "VideoController@update")->middleware('User');
    Route::post('delete', "VideoController@delete")->middleware('User');
    Route::post('priority', "VideoController@priority")->middleware('User');
    Route::post('/', "VideoController@get")->middleware('User');
    Route::post('/{id}', "VideoController@getByID")->middleware('User');
});

Route::group(['prefix' => "ongkir"], function () {
    Route::get('province', "RajaongkirController@province");
    Route::get('province/{provinceID}/city/{cityID?}', "RajaongkirController@city");
    Route::get('cost', "RajaongkirController@cost");
    Route::get('courier', "RajaongkirController@courier");
});

Route::group(['prefix' => "event"], function () {
    Route::post('store', "EventController@store")->middleware('User');
    Route::post('update', "EventController@update")->middleware('User');
    Route::post('delete', "EventController@delete")->middleware('User');
    Route::post('/', "EventController@get")->middleware('User');
});
