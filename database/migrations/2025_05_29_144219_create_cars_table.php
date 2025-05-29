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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->integer('year');
            $table->string('car_type');
            $table->string('fuel_type');
            $table->string('transmission');
            $table->string('mileage')->nullable();
            $table->integer('seats');
            $table->string('color');
            $table->string('registration_number')->unique();
            $table->decimal('daily_rent_price', 10, 2);
            $table->enum('status', ['available', 'rented', 'maintenance'])->default('available');
            $table->string('image');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
