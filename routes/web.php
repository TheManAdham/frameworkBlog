<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ReplyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', [BlogController::class, 'index'])->name('blogs.index');
Route::get('/blog', [BlogController::class, 'index'])->name('blogs.index');
Route::put('blogs/{blog}', [BlogController::class, 'update'])->name('blogs.update');
Route::post('/blog', [BlogController::class, 'store'])->name('blog.store');
Route::post('/blogs/{blog}/reply', [BlogController::class, 'storeReply'])->name('blogs.reply');
Route::resource('blogs', BlogController::class)->except(['create', 'show']);


Route::resource('blogs.replies', ReplyController::class)->only(['update', 'destroy']);
Route::put('/blogs/{blog}/replies/{reply}', [ReplyController::class, 'update'])->name('blogs.replies.update');
Route::delete('/blogs/{blog}/replies/{reply}', [ReplyController::class, 'destroy'])->name('blogs.replies.destroy');
Route::get('blogs/{blog}/edit', [BlogController::class, 'edit'])->name('blogs.edit');
Route::delete('blogs/{blog}', [BlogController::class, 'destroy'])->name('blogs.destroy');



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
