<?php

Route::get('/', 'HomeController@home');
Route::post('/upload', [ 'as' => 'upload', 'uses' => 'HomeController@upload' ]);
Route::get('/admin', [ 'middleware' => 'auth.basic', 'as' => 'admin', 'uses' => 'AdminController@home' ]);
Route::post('/admin/ban', [ 'middleware' => 'auth.basic', 'as' => 'admin.ban', 'uses' => 'AdminController@ban' ]);