<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

    /**
     * Any authenticated user can view Horizon, since admin auth is
     * already enforced by the Filament panel (canAccessPanel on User
     * is the actual gate). Until per-workspace tenants land, the User
     * table only contains operator accounts seeded via the
     * webhook-relay:seed-admin command.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', fn ($user = null) => $user !== null);
    }
}
