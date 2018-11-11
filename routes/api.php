<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('wallet/store', 'Api\WalletController@store');
Route::post('wallet/update', 'Api\WalletController@update');
Route::post('wallet/balance', 'Api\WalletController@balance');

Route::post('transactions', 'Api\TransactionController@view');
Route::post('transactions/store', 'Api\TransactionController@store');
