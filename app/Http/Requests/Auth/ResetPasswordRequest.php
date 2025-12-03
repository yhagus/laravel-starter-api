<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Rules\ValidResetToken;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ResetPasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /** @example admin@admin.com */
            'email' => ['required', 'string', 'email', Rule::exists(User::class, 'email')],
            /** @example password */
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'],
            'token' => ['required', 'string', new ValidResetToken((string) $this->input('email', ''))],
        ];
    }
}
