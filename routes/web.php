<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  return view('welcome');
});

Route::get('avatar/{userId}', [UserController::class, 'getAvatar']);

// FilePond
Route::post('upload', [UserController::class, 'store']);

Route::get('/dashboard', function () {
  return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
  
  Route::prefix('admin')->group(function () {
    // Route::resource('posts', PostController::class);
    Route::resource('posts', PostController::class)->only([
      'index', 'store'
    ]);
  });
  // FilePond
  Route::post('/tmp_upload', [PostController::class, 'tmpUplaod']);
  Route::delete('/tmp_delete', [PostController::class, 'tmpDelete']);
});

require __DIR__.'/auth.php';