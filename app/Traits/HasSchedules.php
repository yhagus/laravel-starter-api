<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Schedule;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait HasSchedules
 * Use this trait in any Eloquent Model to add polymorphic schedule functionality.
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasSchedules
{
    /**
     * Get all schedules for the model.
     *
     * @return MorphMany<Schedule, $this>
     */
    public function schedules(): MorphMany
    {
        return $this->morphMany(Schedule::class, 'schedulable');
    }

    /**
     * Create a new schedule for this model.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function addSchedule(array $attributes): Schedule
    {
        // The return type of create() is inferred as Schedule
        // because schedules() returns MorphMany<Schedule>.
        return $this->schedules()->create($attributes);
    }

    /**
     * Create a daily schedule.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function scheduleDailyAt(string $time, array $attributes = []): Schedule
    {
        return $this->addSchedule(array_merge([
            'frequency' => 'daily',
            'scheduled_time' => $time,
            'name' => 'Daily Schedule',
        ], $attributes));
    }

    /**
     * Create a weekly schedule.
     *
     * @param  list<int>  $days  Array of integers representing days of the week (e.g., 0-6).
     * @param  array<string, mixed>  $attributes
     */
    public function scheduleWeeklyOn(array $days, string $time, array $attributes = []): Schedule
    {
        return $this->addSchedule(array_merge([
            'frequency' => 'weekly',
            'days_of_week' => $days,
            'scheduled_time' => $time,
            'name' => 'Weekly Schedule',
        ], $attributes));
    }

    /**
     * Create a monthly schedule.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function scheduleMonthlyOn(int $day, string $time, array $attributes = []): Schedule
    {
        return $this->addSchedule(array_merge([
            'frequency' => 'monthly',
            'day_of_month' => $day,
            'scheduled_time' => $time,
            'name' => 'Monthly Schedule',
        ], $attributes));
    }

    /**
     * Create a custom cron schedule.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function scheduleWithCron(string $expression, array $attributes = []): Schedule
    {
        return $this->addSchedule(array_merge([
            'frequency' => 'custom',
            'cron_expression' => $expression,
            'name' => 'Custom Schedule',
        ], $attributes));
    }
}

/**
 * Usage examples
 */
// class RunSchedulesCommand extends Command
// {
//    protected $signature = 'schedules:run';
//    protected $description = 'Run all due schedules';
//
//    public function handle()
//    {
//        $schedules = Schedule::due()->get();
//
//        $this->info("Found {$schedules->count()} schedules to run");
//
//        foreach ($schedules as $schedule) {
//            $this->info("Running schedule: {$schedule->name}");
//
//            try {
//                app(ScheduleService::class)->processSchedule($schedule);
//                $this->info("✓ Completed: {$schedule->name}");
//            } catch (\Exception $e) {
//                $this->error("✗ Failed: {$schedule->name} - {$e->getMessage()}");
//            }
//        }
//
//        $this->info('All schedules processed');
//    }
// }
//
// $schedule->command('schedules:run')->everyMinute();
