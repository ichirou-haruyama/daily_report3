<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('report_date')->index();
            $table->string('construction_id', 64)->index();
            $table->text('work_summary');
            $table->string('weather', 32)->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('total_hours', 5, 2)->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft')->index();
            $table->timestamps();

            $table->unique(['user_id', 'report_date', 'construction_id'], 'uniq_user_date_construction');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
