<?php

use Illuminate\Support\Facades\Route;


Route::middleware('guest:api')->group(function () {
    Route::post('/login','AuthController@login');
    Route::post('/register','AuthController@register');
});
