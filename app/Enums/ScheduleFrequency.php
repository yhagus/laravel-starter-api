<?php

declare(strict_types=1);

namespace App\Enums;

enum ScheduleFrequency: string
{
    case ONCE = 'once';
    case MINUTELY = 'minutely';
    case HOURLY = 'hourly';
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';
    case CUSTOM = 'custom';

    /**
     * Get all frequency values as an array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get frequency label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::ONCE => 'One Time',
            self::MINUTELY => 'Every Minute',
            self::HOURLY => 'Hourly',
            self::DAILY => 'Daily',
            self::WEEKLY => 'Weekly',
            self::MONTHLY => 'Monthly',
            self::YEARLY => 'Yearly',
            self::CUSTOM => 'Custom (Cron)',
        };
    }

    /**
     * Get description of the frequency.
     */
    public function description(): string
    {
        return match ($this) {
            self::ONCE => 'Execute only once at the specified time',
            self::MINUTELY => 'Execute every N minutes',
            self::HOURLY => 'Execute every N hours',
            self::DAILY => 'Execute every N days at a specific time',
            self::WEEKLY => 'Execute on specific days of the week',
            self::MONTHLY => 'Execute on a specific day of the month',
            self::YEARLY => 'Execute annually on a specific date',
            self::CUSTOM => 'Execute based on a custom cron expression',
        };
    }

    /**
     * Check if frequency requires a time.
     */
    public function requiresTime(): bool
    {
        return in_array($this, [
            self::ONCE,
            self::DAILY,
            self::WEEKLY,
            self::MONTHLY,
            self::YEARLY,
        ]);
    }

    /**
     * Check if frequency supports intervals.
     */
    public function supportsInterval(): bool
    {
        return in_array($this, [
            self::MINUTELY,
            self::HOURLY,
            self::DAILY,
            self::WEEKLY,
            self::MONTHLY,
            self::YEARLY,
        ]);
    }

    /**
     * Check if frequency requires cron expression.
     */
    public function requiresCron(): bool
    {
        return $this === self::CUSTOM;
    }

    /**
     * Check if frequency requires days of week.
     */
    public function requiresDaysOfWeek(): bool
    {
        return $this === self::WEEKLY;
    }

    /**
     * Check if frequency requires day of month.
     */
    public function requiresDayOfMonth(): bool
    {
        return $this === self::MONTHLY;
    }
}
