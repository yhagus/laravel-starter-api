<?php

declare(strict_types=1);

namespace App\Providers;

use Closure;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;

final class RuleFacadeProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Rule::macro('pairList', fn (string $keyName = 'key', string $valueName = 'value', array $valueRules = []): Closure => static function (string $attribute, mixed $value, Closure $fail) use (
            $valueRules,
            $keyName,
            $valueName
        ): void {
            if (! is_string($value) || mb_trim($value) === '') {
                $fail('The :attribute must be a string.');

                return;
            }

            try {
                /** @var list<array> $pairs */
                $pairs = Str::pairList($value, $keyName, $valueName);
            } catch (RuntimeException) {
                $fail('The :attribute format is invalid.');

                return;
            }

            if ($valueRules === []) {
                return;
            }

            $rules = array_filter(
                [
                    $keyName => $valueRules[$keyName] ?? [],
                    $valueName => $valueRules[$valueName] ?? [],
                ],
                static fn ($rules): bool => $rules !== [],
            );

            if ($rules === []) {
                return;
            }

            foreach ($pairs as $pair) {
                $validator = Validator::make($pair, $rules);

                if ($validator->fails()) {
                    $fail($validator->errors()->first());

                    return;
                }
            }
        });
    }
}
