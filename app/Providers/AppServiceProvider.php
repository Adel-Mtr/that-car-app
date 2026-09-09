<?php

namespace App\Providers;

use App\Contracts\VehicleDataProvider;
use App\Models\User;
use App\Services\DemoVehicleDataProvider;
use App\Services\GovernmentVehicleDataProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(VehicleDataProvider::class, function ($app): VehicleDataProvider {
            return match (config('services.vehicle_data.driver')) {
                'government' => $app->make(GovernmentVehicleDataProvider::class),
                default => $app->make(DemoVehicleDataProvider::class),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());

        Gate::before(function (User $user): ?bool {
            return $user->role === 'admin' ? true : null;
        });

        Gate::define('access-admin', fn (User $user): bool => $user->role === 'admin');

        View::composer('layouts.app', function ($view): void {
            $view->with(
                'unreadNotificationCount',
                auth()->check() ? auth()->user()->unreadNotifications()->count() : 0,
            );
        });
    }
}
