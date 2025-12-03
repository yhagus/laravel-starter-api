<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\ScheduleAction;
use App\Enums\ScheduleFrequency;
use Spatie\LaravelData\Data;

final class ScheduleData extends Data
{
    /**
     * @param  array<mixed>|null  $metadata
     */
    public function __construct(
        public string $name,
        public string $schedulable_type,
        public int|string $schedulable_id,
        public bool $is_active,
        public ?string $description = null,
        public ?ScheduleFrequency $frequency = null,
        public ?bool $is_paused = false,
        public ?ScheduleAction $action = null,
        public ?array $metadata = null,
        public ?string $scheduled_by = null,
    ) {}
}
