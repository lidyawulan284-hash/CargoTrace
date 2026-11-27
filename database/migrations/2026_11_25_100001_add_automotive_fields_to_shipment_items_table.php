<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipment_items', function (Blueprint $table) {
            $table->string('vehicle_model', 150)->nullable()->after('item_description');
            $table->string('vin_number', 50)->nullable()->unique()->after('vehicle_model');
            $table->string('cargo_category', 40)->default('CBU_EV')->index()->after('vin_number');
            $table->decimal('declared_value', 14, 2)->default(0)->after('weight_kg');
        });
    }

    public function down(): void
    {
        Schema::table('shipment_items', function (Blueprint $table) {
            $table->dropUnique(['vin_number']);
            $table->dropIndex(['cargo_category']);
            $table->dropColumn(['vehicle_model', 'vin_number', 'cargo_category', 'declared_value']);
        });
    }
};
