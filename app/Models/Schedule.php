<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ScheduleAction;
use App\Enums\ScheduleFrequency;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Database\Factories\ScheduleFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Schedule extends Model
{
    /**
     * @use HasFactory<ScheduleFactory>
     */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'frequency',
        'cron_expression',
        'scheduled_time',
        'start_date',
        'end_date',
        'days_of_week',
        'day_of_month',
        'interval',
        'last_run_at',
        'next_run_at',
        'run_count',
        'is_active',
        'is_paused',
        'metadata',
        'max_attempts',
        'attempts',
        'action',
        'schedulable_id',
        'schedulable_type',
        'scheduled_by',
    ];

    /**
     * Get the parent schedulable model.
     *
     * @return MorphTo<Model, $this>
     */
    public function schedulable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Check if the schedule should run.
     */
    public function shouldRun(): bool
    {
        if (! $this->is_active || $this->is_paused) {
            return false;
        }

        if ($this->start_date && now()->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && now()->gt($this->end_date)) {
            return false;
        }

        return ! ($this->next_run_at && now()->lt($this->next_run_at));
    }

    /**
     * Calculate the next run time based on frequency.
     */
    public function calculateNextRun(): Carbon|CarbonInterface|null
    {
        $base = \Illuminate\Support\Facades\Date::parse($this->last_run_at ?? now());

        return match ($this->frequency) {
            'minutely' => $base->copy()->addMinutes($this->interval),
            'hourly' => $base->copy()->addHours($this->interval),
            'daily' => $base->copy()->addDays($this->interval),
            'weekly' => $base->copy()->addWeeks($this->interval),
            'monthly' => $base->copy()->addMonths($this->interval),
            'yearly' => $base->copy()->addYears($this->interval),
            'custom' => $this->calculateNextRunFromCron(),
            default => null,
        };
    }

    /**
     * Mark the schedule as run.
     */
    public function markAsRun(): void
    {
        $this->update([
            'last_run_at' => now(),
            'next_run_at' => $this->calculateNextRun(),
            'run_count' => $this->run_count + 1,
            'attempts' => 0,
        ]);
    }

    /**
     * Increment attempt counter.
     */
    public function incrementAttempts(): void
    {
        $this->increment('attempts');

        if ($this->attempts >= $this->max_attempts) {
            $this->update(['is_active' => false]);
        }
    }

    /**
     * Scope to get active schedules.
     *
     * @param  Builder<Schedule>  $query
     * @return Builder<Schedule>
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('is_paused', false);
    }

    /**
     * Scope to get schedules that are due to run.
     *
     * @param  Builder<Schedule>  $query
     * @return Builder<Schedule>
     */
    #[Scope]
    protected function due(Builder $query): Builder
    {
        return $query->active()
            ->where(function ($q): void {
                $q->whereNull('next_run_at')
                    ->orWhere('next_run_at', '<=', now());
            })
            ->where(function ($q): void {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q): void {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    protected function casts(): array
    {
        return [
            'scheduled_time' => 'datetime',
            'start_date' => 'date',
            'end_date' => 'date',
            'days_of_week' => 'array',
            'last_run_at' => 'datetime',
            'next_run_at' => 'datetime',
            'is_active' => 'boolean',
            'is_paused' => 'boolean',
            'metadata' => 'array',
            'action' => ScheduleAction::class,
            'frequency' => ScheduleFrequency::class,
        ];
    }

    /**
     * Calculate next run from cron expression.
     */
    private function calculateNextRunFromCron(): Carbon|CarbonInterface|CarbonImmutable|null
    {
        if (! $this->cron_expression) {
            return null;
        }

        return now()->addHour();
    }
}
