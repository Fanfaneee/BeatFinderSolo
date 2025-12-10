<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\HomePage;
use App\Livewire\AdminMusicManager;
use App\Livewire\Game;

Route::get('/', HomePage::class)->name('home'); 
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/musiques/ajouter', AdminMusicManager::class)->name('admin.musiques.add');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/jeu/{gameId}', Game::class)->name('game');
    

});