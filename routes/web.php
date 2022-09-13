<?php

use App\Http\Controllers\MainController;
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

Route::get('/', [MainController::class, 'home'])->name('home');
Route::post('/check', [MainController::class, 'add']);
Route::put('/edit', [MainController::class, 'put']);
Route::get('/remove', [MainController::class, 'removeAll']);
Route::get('/remove/user/{id}', [MainController::class, 'remove']);
