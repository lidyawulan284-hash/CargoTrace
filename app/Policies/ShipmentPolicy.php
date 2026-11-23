<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;

class ShipmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Shipment $shipment): bool
    {
        if ($user->canManageShipments()) {
            return true;
        }

        return $shipment->customer_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->canManageShipments();
    }

    public function update(User $user, Shipment $shipment): bool
    {
        return $user->canManageShipments();
    }

    public function delete(User $user, Shipment $shipment): bool
    {
        return $user->isAdmin();
    }

    public function addTracking(User $user, Shipment $shipment): bool
    {
        return $user->canManageShipments() && ! $shipment->isTerminal();
    }

    public function manageExceptions(User $user, Shipment $shipment): bool
    {
        return $user->canManageShipments();
    }
}
