<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_report_vehicle_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained('daily_reports')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            // 往路
            $table->time('outbound_start')->nullable();
            $table->time('outbound_end')->nullable();
            $table->string('from_location', 191)->nullable();
            $table->string('to_location', 191)->nullable();
            $table->decimal('distance_km', 8, 1)->nullable();
            $table->boolean('toll_not_used')->default(false);
            $table->string('toll_entry', 191)->nullable();
            $table->string('toll_exit', 191)->nullable();
            $table->unsignedInteger('toll_amount')->nullable();
            // 復路
            $table->time('return_start')->nullable();
            $table->time('return_end')->nullable();
            $table->string('return_from_location', 191)->nullable();
            $table->string('return_to_location', 191)->nullable();
            $table->decimal('return_distance_km', 8, 1)->nullable();
            $table->boolean('return_toll_not_used')->default(false);
            $table->string('return_toll_entry', 191)->nullable();
            $table->string('return_toll_exit', 191)->nullable();
            $table->unsignedInteger('return_toll_amount')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_report_vehicle_costs');
    }
};
