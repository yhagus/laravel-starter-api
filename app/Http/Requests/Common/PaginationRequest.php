<?php

declare(strict_types=1);

namespace App\Http\Requests\Common;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @Inheritable
 */
class PaginationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['string', 'nullable'],
            /** @example 1 */
            /** @default 1 */
            'page' => ['numeric', 'min:1', 'nullable'],
            /** @example 50 */
            /** @default 50 */
            'per_page' => ['numeric', 'nullable', 'min:1', 'max:100'],
        ];
    }
}
