<?php

use Illuminate\Support\Facades\Route;

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
    // return view('welcome');
    return 'welcome';
});

Route::get('/reset-password/{token}/{email}', function ($token, $email) {
    return view('auth.password-reset', ['token' => $token, 'email' => $email]);
})->name('password.reset');
