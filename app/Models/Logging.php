<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasQueryPagination;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Models\Activity;

final class Logging extends Activity
{
    /**
     * @use HasQueryPagination<$this>
     */
    use HasFactory, HasQueryPagination, HasUlids;

    protected function casts(): array
    {
        return [
            'causer_id' => 'string',
            'subject_id' => 'string',
        ];
    }
}
