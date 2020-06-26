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

Route::resource('users', 'User\UserController', ['only'=>['index','show','store','update','destroy']]);
//Route::resource('login', 'Login\LoginController', ['only'=>['store']]);
Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
Route::name('verify')->get('users/verify/{token}', 'User\UserController@verify');
Route::name('verify_phone')->post('users/verify_phone', 'User\UserController@verify_phone');
Route::name('register')->post('register', 'User\UserController@register');
Route::name('forget_passqord')->post('forget_password', 'User\UserController@forget_password');
Route::name('login')->post('login', 'Login\LoginController@store');

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/
