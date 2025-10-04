<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\FunnelController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\AdSpendController;
use App\Http\Controllers\ConversionFunnelController;
use App\Http\Controllers\PerformanceMetricsController;
use App\Http\Controllers\AdSpendAnalyticsController;
use App\Http\Controllers\LeadImportExportController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\TeamManagementController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\WebhookLogsController;
use App\Http\Controllers\ApiDocumentationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/export-csv', [DashboardController::class, 'exportCsv'])->name('export.csv');
Route::post('/ad-spend', [DashboardController::class, 'storeAdSpend'])->name('ad-spend.store');

// Ad Spend routes
Route::resource('ad-spend', AdSpendController::class);
// Removed duplicate/conflicting analytics route for AdSpendController to avoid path collision with AdSpendAnalyticsController
// Route::get('/ad-spend-analytics', [AdSpendController::class, 'analytics'])->name('ad-spend.analytics');
Route::get('/ad-spend/{adSpend}/spend-data', [AdSpendController::class, 'getSpendData'])->name('ad-spend.spend-data');

// Reports routes
Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');

// Conversion Funnel routes
Route::get('/conversion-funnel', [ConversionFunnelController::class, 'index'])->name('conversion-funnel.index');
Route::get('/conversion-funnel/stage-details', [ConversionFunnelController::class, 'getStageDetails'])->name('conversion-funnel.stage-details');

// Performance Metrics routes
    Route::get('/performance-metrics', [PerformanceMetricsController::class, 'index'])->name('performance-metrics.index');
    Route::get('/performance-metrics/export', [PerformanceMetricsController::class, 'export'])->name('performance-metrics.export');

    // Sources routes (consolidated)
    Route::resource('sources', SourceController::class);
    Route::resource('locations', LocationController::class);
    Route::resource('campaigns', CampaignController::class);

// Ad Spend Analytics routes
Route::get('/ad-spend-analytics', [AdSpendAnalyticsController::class, 'index'])->name('ad-spend-analytics.index');
Route::post('/ad-spend-analytics/export', [AdSpendAnalyticsController::class, 'export'])->name('ad-spend-analytics.export');

// Lead Import/Export routes
Route::get('/lead-import-export', [LeadImportExportController::class, 'index'])->name('lead-import-export.index');
Route::post('/lead-import-export/import', [LeadImportExportController::class, 'import'])->name('lead-import-export.import');
Route::post('/lead-import-export/export', [LeadImportExportController::class, 'export'])->name('lead-import-export.export');
Route::get('/lead-import-export/template', [LeadImportExportController::class, 'downloadTemplate'])->name('lead-import-export.template');

// User Profile Routes
Route::get('/profile', [UserProfileController::class, 'index'])->name('user-profile.index');
Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('user-profile.edit');
Route::put('/profile', [UserProfileController::class, 'update'])->name('user-profile.update');
Route::post('/profile/change-password', [UserProfileController::class, 'changePassword'])->name('user-profile.change-password');
Route::post('/profile/update-notifications', [UserProfileController::class, 'updateNotifications'])->name('user-profile.update-notifications');
Route::post('/profile/update-preferences', [UserProfileController::class, 'updatePreferences'])->name('user-profile.update-preferences');
Route::delete('/profile', [UserProfileController::class, 'deleteAccount'])->name('user-profile.delete');
Route::get('/profile/export-data', [UserProfileController::class, 'exportData'])->name('user-profile.export-data');

// Team Management Routes
    Route::get('/team-management', [TeamManagementController::class, 'index'])->name('team-management.index');
    Route::get('/team-management/create', [TeamManagementController::class, 'create'])->name('team-management.create');
    Route::post('/team-management', [TeamManagementController::class, 'store'])->name('team-management.store');
    Route::get('/team-management/{user}', [TeamManagementController::class, 'show'])->name('team-management.show');
    Route::get('/team-management/{user}/edit', [TeamManagementController::class, 'edit'])->name('team-management.edit');
    Route::put('/team-management/{user}', [TeamManagementController::class, 'update'])->name('team-management.update');
    Route::delete('/team-management/{user}', [TeamManagementController::class, 'destroy'])->name('team-management.destroy');
    Route::post('/team-management/{user}/reset-password', [TeamManagementController::class, 'resetPassword'])->name('team-management.reset-password');
    Route::get('/team-management/roles/manage', [TeamManagementController::class, 'roles'])->name('team-management.roles');
    Route::post('/team-management/roles', [TeamManagementController::class, 'storeRole'])->name('team-management.roles.store');
    Route::put('/team-management/roles/{role}', [TeamManagementController::class, 'updateRole'])->name('team-management.roles.update');
    Route::delete('/team-management/roles/{role}', [TeamManagementController::class, 'destroyRole'])->name('team-management.roles.destroy');
    Route::get('/team-management/activity/logs', [TeamManagementController::class, 'activityLogs'])->name('team-management.activity-logs');
    Route::get('/team-management/export', [TeamManagementController::class, 'export'])->name('team-management.export');

    // Settings routes (protected)
    Route::middleware('auth')->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.update-general');
        Route::post('/settings/email', [SettingsController::class, 'updateEmail'])->name('settings.update-email');
        Route::post('/settings/integrations', [SettingsController::class, 'updateIntegrations'])->name('settings.update-integrations');
        Route::post('/settings/security', [SettingsController::class, 'updateSecurity'])->name('settings.update-security');
        Route::post('/settings/test-email', [SettingsController::class, 'testEmail'])->name('settings.test-email');
        Route::get('/settings/clear-cache', [SettingsController::class, 'clearCache'])->name('settings.clear-cache');
        Route::get('/settings/export', [SettingsController::class, 'exportSettings'])->name('settings.export');
    });

    // Webhook Logs routes (protected)
    Route::middleware('auth')->group(function () {
        Route::get('/webhook-logs', [WebhookLogsController::class, 'index'])->name('webhook-logs.index');
        Route::get('/webhook-logs/{id}', [WebhookLogsController::class, 'show'])->name('webhook-logs.show');
        Route::post('/webhook-logs/{id}/retry', [WebhookLogsController::class, 'retry'])->name('webhook-logs.retry');
        Route::post('/webhook-logs/bulk-retry', [WebhookLogsController::class, 'bulkRetry'])->name('webhook-logs.bulk-retry');
        Route::delete('/webhook-logs/clear-old', [WebhookLogsController::class, 'clearOld'])->name('webhook-logs.clear-old');
        Route::get('/webhook-logs/export', [WebhookLogsController::class, 'export'])->name('webhook-logs.export');
    });

    // API Documentation route
    Route::get('/api-documentation', [ApiDocumentationController::class, 'index'])->name('api-documentation.index');

// Lead management routes
Route::resource('leads', LeadController::class);

// Reports routes
Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
Route::get('/reports/export-pdf', [ReportsController::class, 'exportPdf'])->name('reports.export-pdf');
Route::get('/reports/export-excel', [ReportsController::class, 'exportExcel'])->name('reports.export-excel');

// Funnel routes
Route::get('/funnel', [FunnelController::class, 'index'])->name('funnel.index');
Route::get('/funnel/timeline-data', [FunnelController::class, 'getTimelineData'])->name('funnel.timeline-data');

// Performance routes
Route::get('/performance', [PerformanceController::class, 'index'])->name('performance.index');
Route::get('/performance/data', [PerformanceController::class, 'getPerformanceData'])->name('performance.data');

// Additional Sources routes
Route::post('/sources/{source}/toggle-status', [SourceController::class, 'toggleStatus'])->name('sources.toggleStatus');
Route::get('/sources/{source}/performance-data', [SourceController::class, 'getPerformanceData'])->name('sources.getPerformanceData');

// Additional Locations routes
Route::post('/locations/{location}/toggle-status', [LocationController::class, 'toggleStatus'])->name('locations.toggleStatus');
Route::get('/locations/{location}/performance-data', [LocationController::class, 'getPerformanceData'])->name('locations.getPerformanceData');

// Additional Campaigns routes
Route::post('/campaigns/{campaign}/toggle-status', [CampaignController::class, 'toggleStatus'])->name('campaigns.toggleStatus');
Route::get('/campaigns/{campaign}/performance-data', [CampaignController::class, 'getPerformanceData'])->name('campaigns.getPerformanceData');
Route::get('/campaigns/{campaign}/analytics', [CampaignController::class, 'analytics'])->name('campaigns.analytics');
