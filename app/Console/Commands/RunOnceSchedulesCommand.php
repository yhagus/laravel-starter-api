<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Schedules\RunSchedulesCommand;
use App\Enums\ScheduleFrequency;

final class RunOnceSchedulesCommand extends RunSchedulesCommand
{
    protected $signature = 'scheduler:once';

    protected $description = 'Trigger any active one-time schedules immediately.';

    protected function frequency(): ScheduleFrequency
    {
        return ScheduleFrequency::ONCE;
    }
}
