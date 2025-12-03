<?php

declare(strict_types=1);

namespace App\Enums;

enum ScheduleAction: string
{
    case FETCH_PLAYLIST = 'FETCH_PLAYLIST';
    case OTHER = 'OTHER';

    /**
     * Get options
     *
     * @return array<mixed>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $case): array => [
                'label' => $case->label(),
                'value' => $case->value,
            ],
            self::cases()
        );
    }

    public function label(): string
    {
        return match ($this) {
            self::FETCH_PLAYLIST => 'Check Playlist',
            self::OTHER => 'Other',
        };
    }
}
