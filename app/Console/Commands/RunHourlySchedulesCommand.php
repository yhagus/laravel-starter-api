<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Schedules\RunSchedulesCommand;
use App\Enums\ScheduleFrequency;

final class RunHourlySchedulesCommand extends RunSchedulesCommand
{
    protected $signature = 'scheduler:hourly';

    protected $description = 'Trigger all active hourly schedules now.';

    protected function frequency(): ScheduleFrequency
    {
        return ScheduleFrequency::HOURLY;
    }
}
