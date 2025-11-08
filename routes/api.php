<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\EarthquakeController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\SafetyGuideController;
use Illuminate\Support\Facades\Route;

// Public API routes
Route::prefix('v1')->group(function () {
    // Earthquakes
    Route::get('/earthquakes', [EarthquakeController::class, 'index']);
    Route::get('/earthquakes/{earthquake}', [EarthquakeController::class, 'show']);
    Route::post('/earthquakes/refresh', [EarthquakeController::class, 'refresh']);

    // Safety Guides
    Route::get('/safety-guides', [SafetyGuideController::class, 'index']);
    Route::get('/safety-guides/{safetyGuide}', [SafetyGuideController::class, 'show']);

    // Chatbot
    Route::post('/chatbot', [ChatbotController::class, 'chat']);

    // User Preferences (session-based)
    Route::get('/preferences', [PreferenceController::class, 'show']);
    Route::put('/preferences', [PreferenceController::class, 'update']);

    // Alerts
    Route::get('/alerts', [AlertController::class, 'index']);
    Route::get('/alerts/unread-count', [AlertController::class, 'unreadCount']);
    Route::patch('/alerts/{alert}/read', [AlertController::class, 'markAsRead']);
    Route::post('/alerts/mark-all-read', [AlertController::class, 'markAllAsRead']);
});
