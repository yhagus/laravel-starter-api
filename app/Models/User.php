<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasQueryPagination;
use App\Traits\HasSchedules;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\Contracts\OAuthenticatable;
use Laravel\Passport\HasApiTokens;
use Laravel\Scout\Searchable;

final class User extends Authenticatable implements MustVerifyEmail, OAuthenticatable
{
    /**
     * @use HasFactory<UserFactory>
     * @use HasQueryPagination<User>
     */
    use HasApiTokens, HasFactory, HasQueryPagination, HasSchedules, HasUlids, Notifiable, Searchable;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'email_verified_at',
        'password',
        'is_active',
        'remember_token',
        'google_id',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'is_active',
        'google_id',
        'email_verified_at',
    ];

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'name' => 'string',
            'email' => 'string',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'remember_token' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
