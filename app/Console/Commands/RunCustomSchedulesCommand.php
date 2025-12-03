<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Schedules\RunSchedulesCommand;
use App\Enums\ScheduleFrequency;

final class RunCustomSchedulesCommand extends RunSchedulesCommand
{
    protected $signature = 'scheduler:custom';

    protected $description = 'Trigger all active custom (cron-based) schedules now.';

    protected function frequency(): ScheduleFrequency
    {
        return ScheduleFrequency::CUSTOM;
    }
}
