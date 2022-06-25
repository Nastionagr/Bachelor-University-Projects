<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\QuestionsController;
use App\Http\Controllers\AnswersController;

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

Route::get('/', [ProductController::class, 'home'])->name('/');
Route::get('/category/{category}', [ProductController::class, 'showCategory'])->where('category','^[a-zA-Z0-9-_\/]+$');
Route::resource('products', ProductController::class);

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'login_form']);
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'register_form']);
Route::get('/logout', [AuthController::class, 'logout'])->name('register');

Route::get('/survey', [QuestionsController::class, 'show_true'])->name('show_true');;
Route::post('/survey', [QuestionsController::class, 'store']);

Route::get('/cart', [CartController::class, 'index']);
Route::get('/cart/info', [CartController::class, 'info']);
Route::get('/cart/summary/{id}', [CartController::class, 'summary']);
Route::post('/cart/info', [CartController::class, 'fillInfo']);
Route::get('/cart/add/{id}', [CartController::class, 'add']);
Route::patch('/cart/update', [CartController::class, 'update']);
Route::delete('/cart/remove/{id}', [CartController::class, 'remove']);

Route::get('/admin', [AdminController::class, 'index']);
Route::get('/admin/new', [AdminController::class, 'create']);
Route::post('/admin/new', [AdminController::class, 'create_form']);
Route::get('/admin/edit/{id}', [AdminController::class, 'edit']);
Route::post('/admin/edit/{id}', [AdminController::class, 'edit_form']);
Route::post('/admin/edit/{id}/image', [AdminController::class, 'upload_image']);
Route::delete('/admin/delete/{id}', [AdminController::class, 'delete']);