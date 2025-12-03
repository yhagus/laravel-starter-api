<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use RuntimeException;

final class StringFacadeProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Str::macro('pairList', function (string $string, string $keyName, string $valueName): array {
            $entries = array_filter(
                array_map(
                    trim(...),
                    explode(',', $string),
                ),
                static fn (string $segment): bool => $segment !== '',
            );

            throw_if($entries === [], RuntimeException::class, 'The provided string is not in the expected pair list format.');

            return array_map(
                static function (string $entry) use ($keyName, $valueName): array {
                    $pair = array_map(
                        trim(...),
                        explode(':', $entry, 2),
                    );

                    throw_if(count($pair) !== 2 || $pair[0] === '' || $pair[1] === '', RuntimeException::class, 'The provided string is not in the expected pair list format.');

                    return [
                        $keyName => $pair[0],
                        $valueName => $pair[1],
                    ];
                },
                array_values($entries),
            );
        });
    }
}
