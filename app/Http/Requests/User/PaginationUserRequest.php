<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Http\Requests\Common\PaginationRequest;
use Illuminate\Validation\Rule;

final class PaginationUserRequest extends PaginationRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currentRules = parent::rules();

        return array_merge($currentRules, [
            /** @example first_name:asc,last_name:desc,... */
            'orders' => [
                'nullable',
                'string',
                Rule::pairList(
                    keyName: 'column',
                    valueName: 'direction',
                    valueRules: [
                        'column' => ['required', Rule::in(['first_name', 'last_name', 'email', 'created_at', 'updated_at'])],
                        'direction' => ['required', Rule::in(['asc', 'desc'])],
                    ],
                ),
            ],
        ]);
    }
}
