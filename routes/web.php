<?php

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

Route::get('/', function () {
    die('kltransport api');
});

Route::group(['namespace' => '\Simulation', 'prefix' => '/simulation'], function(){
    Route::get('/lines', 'LineController@getLines');
    Route::get('/nodes', 'LineController@getNodes');

});

Route::group(['namespace' => '\User\Api'], function () {
    Route::get('/dummy/order', 'OrderController@dummy');
});
