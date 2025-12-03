<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Password;

final readonly class ValidResetToken implements ValidationRule
{
    public function __construct(private string $email) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @var User|null $user */
        $user = User::query()->where('email', $this->email)->first();

        if ($user === null) {
            $fail(__('The selected email is invalid.'));

            return;
        }

        $token = is_string($value) ? $value : '';

        if (! Password::broker()->tokenExists($user, $token)) {
            $fail(__('The password reset token is invalid or has expired.'));
        }
    }
}
