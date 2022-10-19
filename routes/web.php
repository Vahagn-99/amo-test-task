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
    return view('welcome');
});

Route::get('/get-token', [\App\Http\Controllers\AmoController::class, 'getToken'])->name('amo.get-token');
Route::get('/show-amo-button', [\App\Http\Controllers\AmoController::class, 'amoButton'])->name('amo.button');
