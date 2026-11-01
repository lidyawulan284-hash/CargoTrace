<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // e.g. IDJKT, SGSIN, NLRTM
            $table->string('name', 150); // e.g. Port of Tanjung Priok
            $table->string('type', 40)->default('PORT'); // PORT, AIRPORT, WAREHOUSE, CUSTOMS_FACILITY, HUB
            $table->string('address')->nullable();
            $table->string('city', 100);
            $table->string('country', 100);
            $table->string('country_code', 10)->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
