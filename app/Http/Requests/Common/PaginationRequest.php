<?php

declare(strict_types=1);

namespace App\Http\Requests\Common;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class PaginationRequest extends FormRequest
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
            'page' => ['numeric', 'min:1', 'nullable'],
            'per_page' => ['numeric', 'nullable', 'max:100'],
        ];
    }
}
