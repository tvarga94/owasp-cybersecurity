<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::post('/login', 'Auth\LoginController@login');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
Route::get('/admin/users/{user}/edit', [AdminController::class, 'edit'])->name('admin.users.edit');
Route::put('/admin/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
Route::delete('/admin/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');

require __DIR__.'/auth.php';
