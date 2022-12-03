<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

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

Route::get('/task1', [OrderController::class, 'index']);
Route::get('/task1/{id}', [OrderController::class, 'show']);
Route::get('/task2', [OrderController::class, 'rank']);
Route::get('/typeahead_autocomplete/action', [OrderController::class, 'autocomplete'])->name('typeahead_autocomplete.action');

