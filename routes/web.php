<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GamificationController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\EarthquakeController;
use App\Http\Controllers\DisasterController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\UserPreferenceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\DisasterManagementController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', [DisasterController::class, 'index'])->name('dashboard');

// Citizen Reporting
Route::get('/report-disaster', [App\Http\Controllers\CitizenReportController::class, 'create'])->name('citizen.report');
Route::post('/report-disaster', [App\Http\Controllers\CitizenReportController::class, 'store'])->name('citizen.report.store');

// Public API endpoints for ResQHub
Route::prefix('api')->group(function () {
    // Earthquakes
    Route::get('earthquakes', [EarthquakeController::class, 'list']);
    Route::get('earthquakes/{earthquake}', [EarthquakeController::class, 'show']);
    Route::get('earthquakes/nearby', [EarthquakeController::class, 'nearby']);
    Route::post('earthquakes/refresh', [EarthquakeController::class, 'refresh']);

    // Multi-Disaster Monitoring
    Route::get('disasters', [DisasterController::class, 'list']);
    Route::get('disasters/{disaster}', [DisasterController::class, 'show']);
    Route::get('disasters/type/{type}', [DisasterController::class, 'byType']);
    Route::get('disasters/nearby', [DisasterController::class, 'nearby']);

    // Alerts
    Route::get('alerts', [AlertController::class, 'index']);
    Route::get('alerts/unread-count', [AlertController::class, 'unreadCount']);
    Route::post('alerts/{alert}/read', [AlertController::class, 'markAsRead']);
    Route::post('alerts/read-all', [AlertController::class, 'markAllAsRead']);

    // Chatbot
    Route::post('chatbot', [ChatbotController::class, 'chat']);

    // Preferences
    Route::get('preferences', [UserPreferenceController::class, 'show']);
    Route::post('preferences', [UserPreferenceController::class, 'update']);

    // Citizen Reports
    Route::get('citizen-reports/pending', [App\Http\Controllers\CitizenReportController::class, 'pending']);
    Route::get('citizen-reports/verified', [App\Http\Controllers\CitizenReportController::class, 'verified']);
    Route::get('citizen-reports/rejected', [App\Http\Controllers\CitizenReportController::class, 'rejected']);
    Route::post('citizen-reports/{report}/verify', [App\Http\Controllers\CitizenReportController::class, 'verify']);
    Route::post('citizen-reports/{report}/reject', [App\Http\Controllers\CitizenReportController::class, 'reject']);
    Route::delete('citizen-reports/{report}', [App\Http\Controllers\CitizenReportController::class, 'destroy']);
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('disasters', [DisasterManagementController::class, 'index'])->name('disasters');
    Route::get('disasters/create', [DisasterManagementController::class, 'create'])->name('disasters.create');
    Route::get('disasters/verify', [DisasterManagementController::class, 'verifyPage'])->name('disasters.verify.page');
    Route::get('disasters/simulate', [DisasterManagementController::class, 'simulate'])->name('disasters.simulate');
    Route::post('disasters/simulate-alert', [DisasterManagementController::class, 'simulateAlert'])->name('disasters.simulate-alert');
    Route::post('disasters/stop-test-alerts', [DisasterManagementController::class, 'stopTestAlerts'])->name('disasters.stop-test-alerts');
    Route::get('disasters/stats', [DisasterManagementController::class, 'stats'])->name('disasters.stats');
    Route::get('disasters/list', [DisasterManagementController::class, 'disasters'])->name('disasters.list');
    Route::post('disasters', [DisasterManagementController::class, 'store'])->name('disasters.store');
    Route::put('disasters/{disaster}', [DisasterManagementController::class, 'update'])->name('disasters.update');
    Route::delete('disasters/{disaster}', [DisasterManagementController::class, 'destroy'])->name('disasters.destroy');
    Route::post('disasters/{disaster}/verify', [DisasterManagementController::class, 'verify'])->name('disasters.verify');
    Route::post('disasters/{disaster}/resolve', [DisasterManagementController::class, 'resolve'])->name('disasters.resolve');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Teams
    Route::get('teams', [TeamController::class, 'index'])->name('teams.index');
    Route::get('teams/create', [TeamController::class, 'create'])->name('teams.create');
    Route::post('teams', [TeamController::class, 'store'])->name('teams.store');
    Route::get('teams/{team}', [TeamController::class, 'show'])->name('teams.show');
    Route::post('teams/join', [TeamController::class, 'join'])->name('teams.join');
    Route::delete('teams/{team}/leave', [TeamController::class, 'leave'])->name('teams.leave');

    // Team Activities
    Route::get('teams/{team}/activities', [ActivityController::class, 'index'])->name('teams.activities.index');
    Route::get('teams/{team}/activities/create', [ActivityController::class, 'create'])->name('teams.activities.create');
    Route::post('teams/{team}/activities', [ActivityController::class, 'store'])->name('teams.activities.store');
    Route::get('teams/{team}/activities/{activity}', [ActivityController::class, 'show'])->name('teams.activities.show');
    Route::post('teams/{team}/activities/{activity}/tasks', [ActivityController::class, 'createTask'])->name('teams.activities.tasks.store');
    Route::patch('teams/{team}/activities/{activity}/tasks/{task}/status', [ActivityController::class, 'updateTaskStatus'])->name('teams.activities.tasks.status');
    Route::patch('teams/{team}/activities/{activity}/tasks/{task}/verify', [ActivityController::class, 'verifyTask'])->name('teams.activities.tasks.verify');

    // Gamification
    Route::get('teams/{team}/leaderboard', [GamificationController::class, 'leaderboard'])->name('teams.leaderboard');
    Route::get('teams/{team}/badges', [GamificationController::class, 'badges'])->name('teams.badges');
    Route::get('teams/{team}/points', [GamificationController::class, 'points'])->name('teams.points');
    Route::post('teams/{team}/badges', [GamificationController::class, 'createBadge'])->name('teams.badges.store');
    Route::post('teams/{team}/badges/{badge}/award', [GamificationController::class, 'awardBadge'])->name('teams.badges.award');
    Route::post('teams/{team}/points', [GamificationController::class, 'addPoints'])->name('teams.points.store');

    // Shop
    Route::get('shop', [ShopController::class, 'index'])->name('shop.index');
    Route::post('shop/purchase', [ShopController::class, 'purchase'])->name('shop.purchase');

    // Accounts
    Route::inertia('accounts', 'Accounts')->name('accounts.index');

    // Admin Dashboard
    Route::inertia('admin', 'admin/Dashboard')->name('admin.dashboard');
    Route::middleware(['is_admin'])->group(function () {
        Route::resource('admin/accounts', AccountController::class)->except(['create', 'edit', 'show']);
    });
    Route::inertia('admin/teams', 'admin/Teams')->name('admin.teams');
    Route::inertia('admin/shop', 'admin/Shop')->name('admin.shop');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
