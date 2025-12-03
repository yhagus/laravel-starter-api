<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Schedules\RunSchedulesCommand;
use App\Enums\ScheduleFrequency;

final class RunMonthlySchedulesCommand extends RunSchedulesCommand
{
    protected $signature = 'scheduler:monthly';

    protected $description = 'Trigger all active monthly schedules now.';

    protected function frequency(): ScheduleFrequency
    {
        return ScheduleFrequency::MONTHLY;
    }
}
