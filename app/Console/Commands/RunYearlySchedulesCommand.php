<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Schedules\RunSchedulesCommand;
use App\Enums\ScheduleFrequency;

final class RunYearlySchedulesCommand extends RunSchedulesCommand
{
    protected $signature = 'scheduler:yearly';

    protected $description = 'Trigger all active yearly schedules now.';

    protected function frequency(): ScheduleFrequency
    {
        return ScheduleFrequency::YEARLY;
    }
}
