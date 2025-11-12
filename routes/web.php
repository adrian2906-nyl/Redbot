<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth','verified'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Vista del chat
    Route::view('/chat', 'chat')->name('chat');

    // Endpoint del chat (rate limit)
    Route::post('/chat/cisco', [ChatController::class, 'responder'])
        ->middleware('throttle:30,1')  // 30 req/min (ajusta a tu gusto)
        ->name('chat.cisco');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
