<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Schedules\RunSchedulesCommand;
use App\Enums\ScheduleFrequency;

final class RunMinutelySchedulesCommand extends RunSchedulesCommand
{
    protected $signature = 'scheduler:minutely';

    protected $description = 'Trigger all active minutely schedules now.';

    protected function frequency(): ScheduleFrequency
    {
        return ScheduleFrequency::MINUTELY;
    }
}
