<?php

namespace App\Providers;

use App\Http\Middleware\CekJabatanPanitia;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
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
        Gate::define('edit-jabatan-panitia', function ($user) {
            return auth('admin')->check(); // Hanya admin yang bisa
        });
    }
}
