<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\LandingController;
use App\Http\Controllers\User\SubmissionController;
use App\Http\Controllers\User\ThreadController;
use App\Http\Controllers\User\FileController;

// User-facing (no auth accounts)
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/first', [SubmissionController::class, 'create'])->name('first.create');
Route::post('/first', [SubmissionController::class, 'store'])->name('first.store');
Route::get('/passcode', [ThreadController::class, 'enterPasscode'])->name('thread.enter');
Route::post('/passcode', [ThreadController::class, 'authenticate'])->name('thread.auth');

Route::middleware('thread.session')->group(function () {
    Route::get('/thread', [ThreadController::class, 'show'])->name('thread.show');
    Route::post('/thread/message', [ThreadController::class, 'addMessage'])->name('thread.message.add');
    Route::delete('/thread/message/{message}', [ThreadController::class, 'deleteMessage'])->name('thread.message.delete');
    Route::post('/logout', [ThreadController::class, 'logout'])->name('thread.logout');
    Route::get('/file/{message}/download', [FileController::class, 'download'])->name('file.download');
});
