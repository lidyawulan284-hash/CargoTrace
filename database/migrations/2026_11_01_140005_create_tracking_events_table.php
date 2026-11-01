<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->onDelete('cascade');
            $table->string('event_type', 50); // CREATED, BOOKED, PICKED_UP, DEPARTED, IN_TRANSIT, ARRIVED, DELIVERED, EXCEPTION
            $table->string('status', 30); // Corresponding shipment status at this step
            $table->string('location_name', 150);
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->dateTime('event_time');
            $table->text('description');
            $table->boolean('is_exception')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['shipment_id', 'event_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_events');
    }
};
