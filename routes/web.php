<?php

Route::group(['middleware' => 'tenancy.enforce'], function () {
    Auth::routes();
	Route::get('/', 'IndexController@index')->name('home');
});
