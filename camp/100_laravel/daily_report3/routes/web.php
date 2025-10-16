<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Models\DailyReport;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('dashboard', function () {
    $query = DailyReport::with(['user', 'vehicleCost.tolls'])->orderByDesc('created_at');

    $user = request()->user();
    if ($user?->role !== 'admin') {
        $query->where('user_id', $user?->id);
    }

    $reports = $query->paginate(20);

    return view('dashboard', [
        'reports' => $reports,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

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

    // 作業日報 作成/車両費/その他
    Volt::route('reports/create', 'reports.create')->name('reports.create');
    Volt::route('reports/vehicle-costs', 'reports.vehicle-costs')->name('reports.vehicle_costs');
    Volt::route('reports/vehicle-costs/confirm', 'reports.vehicle-costs-confirm')->name('reports.vehicle_costs.confirm');
    Volt::route('reports/other', 'reports.other')->name('reports.other');
});

require __DIR__ . '/auth.php';
