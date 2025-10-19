<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->morphs('schedulable');
            $table->text('description')->nullable();
            $table->string('frequency');
            $table->string('cron_expression')->nullable(); // used on custom
            $table->time('scheduled_time')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('days_of_week')->nullable();
            $table->unsignedTinyInteger('day_of_month')->nullable();
            $table->unsignedInteger('interval')->default(1);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->unsignedInteger('run_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_paused')->default(false);
            $table->json('metadata')->nullable();
            $table->unsignedTinyInteger('max_attempts')->default(3);
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamps();

            $table->index(['schedulable_type', 'schedulable_id']);
            $table->index('next_run_at');
            $table->index(['is_active', 'is_paused']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
