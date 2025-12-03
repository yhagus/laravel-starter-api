<?php

declare(strict_types=1);

namespace App\Console\Commands\Schedules;

use App\Enums\ScheduleAction;
use App\Enums\ScheduleFrequency;
use App\Events\ScheduleFetchPlaylistRequested;
use App\Models\Schedule;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

abstract class RunSchedulesCommand extends Command
{
    abstract protected function frequency(): ScheduleFrequency;

    final public function handle(): int
    {
        $schedules = $this->schedulesForFrequency();

        if ($schedules->isEmpty()) {
            $this->info(sprintf('No %s schedules ready to trigger.', $this->frequency()->value));

            return self::SUCCESS;
        }

        foreach ($schedules as $schedule) {
            $this->line(sprintf(
                'Triggering %s (%s)',
                $schedule->name ?? 'Unnamed schedule',
                $schedule->id,
            ));

            $this->dispatchAction($schedule);
            $schedule->markAsRun();
        }

        $this->info(sprintf('Triggered %d %s schedule(s).', $schedules->count(), $this->frequency()->value));

        return self::SUCCESS;
    }

    /**
     * @return Collection<int, Schedule>
     */
    protected function schedulesForFrequency(): Collection
    {
        return Schedule::query()
            ->active()
            ->where('frequency', $this->frequency()->value)
            ->get();
    }

    protected function dispatchAction(Schedule $schedule): void
    {
        Log::debug('Event dispatched', $schedule->toArray());
        /**
         * Dispatch event of fetch playlist
         */
        if ($schedule->action === ScheduleAction::FETCH_PLAYLIST) {
            event(new ScheduleFetchPlaylistRequested($schedule));
        }
    }
}
