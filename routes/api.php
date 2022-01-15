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