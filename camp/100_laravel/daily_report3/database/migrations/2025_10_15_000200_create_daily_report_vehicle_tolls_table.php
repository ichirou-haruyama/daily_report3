<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_report_vehicle_tolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_cost_id')->constrained('daily_report_vehicle_costs')->cascadeOnDelete();
            $table->enum('direction', ['outbound', 'return'])->index();
            $table->time('entry_time')->nullable();
            $table->string('entry_ic', 191);
            $table->time('exit_time')->nullable();
            $table->string('exit_ic', 191);
            $table->unsignedInteger('amount')->nullable();
            $table->string('method', 50)->nullable(); // ETC, 現金など
            $table->timestamps();
            $table->index(['vehicle_cost_id', 'direction']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_report_vehicle_tolls');
    }
};
