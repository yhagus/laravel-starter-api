<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Schedules\RunSchedulesCommand;
use App\Enums\ScheduleFrequency;

final class RunWeeklySchedulesCommand extends RunSchedulesCommand
{
    protected $signature = 'scheduler:weekly';

    protected $description = 'Trigger all active weekly schedules now.';

    protected function frequency(): ScheduleFrequency
    {
        return ScheduleFrequency::WEEKLY;
    }
}
