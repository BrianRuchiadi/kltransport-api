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

Route::group(['namespace' => '\User\Api'], function() {
    Route::get('/lines', 'LineController@getLines');
    Route::get('/stations', 'StationController@getStations');
    Route::get('/fares', 'FareController@getFares');
    Route::get('/routes/{nodeOne}/{nodeTwo}/details', 'RouteController@getRouteDetails');
});

// This group is used for testing simulation only
Route::group(['namespace' => '\Simulation', 'prefix' => '/simulation'], function() {
    Route::get('/lines', 'LineController@getLines');
    Route::get('/nodes', 'LineController@getNodes');

    Route::get('/fares/generate', 'FareController@generateRouteFare');

    Route::get('/fares/{node}/cashless', 'FareController@getCashlessFaresByNodeId');
    Route::post('/fares/update', 'FareController@createOrUpdateCashlessFares');

    Route::get('/routes/display/{nodeOne}/{nodeTwo}', 'RouteController@displayRoutes');
    Route::get('/routes/generate/{node}', 'RouteController@generateRouteByNodeId');
    Route::get('/routes/generate', 'RouteController@generateRouteTransit');
});

Route::group(['namespace' => '\User\Api'], function () {
    Route::get('/dummy/order', 'OrderController@dummy');
});
