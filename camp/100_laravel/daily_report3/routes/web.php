<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    // 作業日報（メモ）
    Volt::route('memos', 'memos.index')->name('memos.index');
    Volt::route('memos/{memo}', 'memos.show')->name('memos.show');

    // 作業日報検索
    Volt::route('reports/search', 'reports.search')->name('reports.search');
});

require __DIR__ . '/auth.php';
