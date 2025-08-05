<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('proximity_logs', function (Blueprint $table) {
        $table->id();
        $table->decimal('delivery_lat', 10, 7);
        $table->decimal('delivery_lng', 10, 7);
        $table->integer('radius_meters');
        $table->decimal('distance_meters', 10, 2);
        $table->boolean('is_within_range');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proximity_logs');
    }
};
