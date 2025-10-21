<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class LoginRequest extends FormRequest
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
            /** @example admin123 */
            'password' => ['required', 'string'],
        ];
    }
}
