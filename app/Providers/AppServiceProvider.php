<?php

namespace App\Providers;

use App\Models\Letter;
use App\Models\Report;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('id');

        View::composer('layouts.app', function ($view) {
            if (! auth()->check()) {
                return;
            }

            $userId = auth()->id();

            $pendingReports = Report::where('status', 'pending');
            if (! auth()->user()->canReview()) {
                $pendingReports->where('user_id', $userId);
            }
            $pendingCount = (clone $pendingReports)->count();

            $newLettersCount = Letter::where('user_id', $userId)
                ->where('status', 'baru')
                ->count();

            $pendingPreview = (clone $pendingReports)->with('user')
                ->latest()
                ->take(4)
                ->get();

            $view->with(compact('pendingCount', 'newLettersCount', 'pendingPreview'));
        });
    }
}
