<?php

declare(strict_types=1);

namespace App\Actions\User;

use Illuminate\Support\Facades\DB;

final readonly class CreateUserAction
{
    /**
     * Execute the action.
     */
    public function handle(): void
    {
        DB::transaction(function (): void {
            //
        });
    }
}
