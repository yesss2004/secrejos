<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SubActivityPlanController;
use App\Http\Controllers\TargetScheduleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::middleware('role:admin,operator')->group(function () {
        Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
        Route::get('/pencarian', SearchController::class)->name('search');
        Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
    });

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/create', [ReportController::class, 'create'])->middleware('role:admin,operator')->name('reports.create');
    Route::get('/reports/{report}/print', [ReportController::class, 'print'])->name('reports.print');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::patch('/reports/{report}/review', [ReportController::class, 'review'])->middleware('role:admin,verifikator')->name('reports.review');
    Route::post('/reports', [ReportController::class, 'store'])->middleware('role:admin,operator')->name('reports.store');
    Route::get('/reports/{report}/edit', [ReportController::class, 'edit'])->middleware('role:admin,operator')->name('reports.edit');
    Route::put('/reports/{report}', [ReportController::class, 'update'])->middleware('role:admin,operator')->name('reports.update');
    Route::patch('/reports/{report}', [ReportController::class, 'update'])->middleware('role:admin,operator');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->middleware('role:admin,operator')->name('reports.destroy');

    Route::get('/perencanaan', [SubActivityPlanController::class, 'index'])->name('plans.index');
    Route::get('/perencanaan/{plan}/print', [SubActivityPlanController::class, 'print'])->name('plans.print');
    Route::middleware('role:admin,operator')->group(function () {
        Route::get('/perencanaan/create', [SubActivityPlanController::class, 'create'])->name('plans.create');
        Route::post('/perencanaan', [SubActivityPlanController::class, 'store'])->name('plans.store');
        Route::get('/perencanaan/{plan}/edit', [SubActivityPlanController::class, 'edit'])->name('plans.edit');
        Route::put('/perencanaan/{plan}', [SubActivityPlanController::class, 'update'])->name('plans.update');
        Route::delete('/perencanaan/{plan}', [SubActivityPlanController::class, 'destroy'])->name('plans.destroy');
    });
    Route::get('/perencanaan/{plan}', [SubActivityPlanController::class, 'show'])->name('plans.show');

    Route::get('/lampiran/{attachment}/download', [ReportController::class, 'download'])->name('attachments.download');
    Route::delete('/lampiran/{attachment}', [ReportController::class, 'destroyAttachment'])->middleware('role:admin,operator')->name('attachments.destroy');

    Route::middleware('role:admin,operator')->group(function () {
        Route::get('/letters/export', [LetterController::class, 'export'])->name('letters.export');
        Route::patch('/letters/{letter}/status', [LetterController::class, 'updateStatus'])->name('letters.status');
        Route::get('/letters/{letter}/download', [LetterController::class, 'download'])->name('letters.download');
        Route::resource('letters', LetterController::class);
        Route::resource('targets', TargetScheduleController::class)->except(['show']);
        Route::patch('/targets/{target}/status', [TargetScheduleController::class, 'updateStatus'])->name('targets.status');
    });

    Route::middleware('role:admin,operator,verifikator')->group(function () {
        Route::get('/kendala', [IssueController::class, 'index'])->name('issues.index');
        Route::patch('/kendala/{issue}/verify', [IssueController::class, 'verify'])->middleware('role:admin,verifikator')->name('issues.verify');
    });
    Route::middleware('role:admin,operator')->group(function () {
        Route::get('/kendala/create', [IssueController::class, 'create'])->name('issues.create');
        Route::post('/kendala', [IssueController::class, 'store'])->name('issues.store');
        Route::get('/kendala/{issue}/edit', [IssueController::class, 'edit'])->name('issues.edit');
        Route::put('/kendala/{issue}', [IssueController::class, 'update'])->name('issues.update');
        Route::patch('/kendala/{issue}', [IssueController::class, 'update']);
        Route::delete('/kendala/{issue}', [IssueController::class, 'destroy'])->name('issues.destroy');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}', [UserController::class, 'update']);
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
