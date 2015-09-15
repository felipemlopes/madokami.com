<?php

Route::get('/', 'HomeController@home');
Route::post('/upload', [ 'as' => 'upload', 'uses' => 'HomeController@upload' ]);