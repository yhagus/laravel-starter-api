<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

final class Media extends BaseMedia
{
    /**
     * @use HasFactory<MediaFactory>
     */
    use HasFactory, HasUlids;
}
