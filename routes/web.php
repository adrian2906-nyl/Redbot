<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController; // ✅ Importar tu controlador
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 🔹 Ruta del Chat (vista)
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', function () {
        return view('chat');
    })->name('chat');

    // 🔹 Ruta POST para que el chat.blade.php hable con tu controlador
    Route::post('/chat/cisco', [ChatController::class, 'responder'])->name('chat.cisco');
});
Route::post('/chat/cisco', [App\Http\Controllers\ChatController::class, 'responder'])
    ->middleware('auth')
    ->name('chat.cisco');


// 🔹 Perfil de usuario
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
