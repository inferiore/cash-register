<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix("cash-register")->group(function(){
   Route::post("charge","CashRegisterController@charge");
   Route::post("empty","CashRegisterController@empty");
   Route::post("add-payment","CashRegisterController@addPayment");
   Route::get("status","CashRegisterController@status");
   Route::get("logs","CashRegisterController@logs");



});
