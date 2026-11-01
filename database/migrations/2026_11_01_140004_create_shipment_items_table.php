<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->onDelete('cascade');
            $table->string('item_description', 200);
            $table->integer('quantity')->default(1);
            $table->decimal('weight_kg', 10, 2)->default(0);
            $table->string('dimensions', 60)->nullable(); // e.g. 120x80x160 cm
            $table->string('hs_code', 30)->nullable(); // Harmonized System Code
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_items');
    }
};
