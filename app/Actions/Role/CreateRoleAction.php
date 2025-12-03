<?php

declare(strict_types=1);

namespace App\Actions\Role;

use Illuminate\Support\Facades\DB;

final readonly class CreateRoleAction
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
