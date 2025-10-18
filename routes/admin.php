<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\MessageController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::middleware('admin.auth')->group(function () {
    Route::get('/', [MessageController::class, 'index'])->name('admin.messages.index');
    Route::get('/threads/{thread}', [MessageController::class, 'show'])->name('admin.messages.show');
    Route::post('/threads/{thread}/message', [MessageController::class, 'addMessage'])->name('admin.messages.add');
    Route::post('/threads/{thread}/status', [MessageController::class, 'updateStatus'])->name('admin.messages.status');
    Route::post('/threads/{thread}/note', [MessageController::class, 'addNote'])->name('admin.messages.note');
    Route::get('/file/{thread}/{message}/download', [MessageController::class, 'download'])->name('admin.file.download');
});
