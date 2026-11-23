<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number', 50)->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('carrier_id')->constrained('carriers')->onDelete('restrict');
            $table->foreignId('origin_id')->constrained('locations')->onDelete('restrict');
            $table->foreignId('destination_id')->constrained('locations')->onDelete('restrict');

            $table->string('cargo_type', 60)->default('GENERAL_CARGO');
            $table->decimal('total_weight_kg', 10, 2)->default(0);
            $table->decimal('total_volume_cbm', 10, 2)->default(0);

            $table->dateTime('expected_departure');
            $table->dateTime('expected_arrival');
            $table->dateTime('actual_departure')->nullable();
            $table->dateTime('actual_arrival')->nullable();
            $table->dateTime('estimated_arrival')->nullable();

            // Status: CREATED, BOOKED, PICKED_UP, IN_TRANSIT, DELIVERED, CANCELLED
            $table->string('status', 30)->default('CREATED')->index();

            // Deterministic Delay Evaluation (Slide 8)
            $table->boolean('is_delayed')->default(false)->index();
            $table->integer('delay_hours')->default(0);
            $table->string('delay_severity', 20)->default('NONE'); // NONE, LOW, MEDIUM, HIGH

            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
