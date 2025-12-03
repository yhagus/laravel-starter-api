<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Schedules\RunSchedulesCommand;
use App\Enums\ScheduleFrequency;

final class RunDailySchedulesCommand extends RunSchedulesCommand
{
    protected $signature = 'scheduler:daily';

    protected $description = 'Trigger all active daily schedules now.';

    protected function frequency(): ScheduleFrequency
    {
        return ScheduleFrequency::DAILY;
    }
}
