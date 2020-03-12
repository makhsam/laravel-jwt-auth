<?php

Route::post('auth/login', 'LoginApiController@login');

// Another example for Authentication
Route::post('auth/register', 'AuthController@register');
Route::post('auth/login', 'AuthController@login');
Route::post('auth/logout', 'AuthController@logout');
Route::post('auth/password', 'AuthController@resendPassword');


Route::group(['middleware' => 'auth.jwt'], function() {

    Route::get('users', 'UserController@index');

    // other auth routes ...
});