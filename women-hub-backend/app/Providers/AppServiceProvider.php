<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
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
        View::composer('mentor.*', function ($view) {
            $unreadCount = 0;
            $unreadNotifications = collect();

            $user = Auth::user();
            if ($user) {
                $notifications = Notification::where('user_id', $user->id)
                    ->whereNull('read_at');

                $unreadCount = $notifications->count();
                $unreadNotifications = $notifications->latest()->get();
            }

            $view->with([
                'unreadCount' => $unreadCount,
                'unreadNotifications' => $unreadNotifications,
            ]);
        });
    }
}
