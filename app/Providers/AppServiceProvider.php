<?php

namespace App\Providers;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use App\Policies\CarrierPolicy;
use App\Policies\LocationPolicy;
use App\Policies\ShipmentPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Shipment::class, ShipmentPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Carrier::class, CarrierPolicy::class);
        Gate::policy(Location::class, LocationPolicy::class);
    }
}
