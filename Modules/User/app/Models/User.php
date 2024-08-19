<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Modules\Shared\Models\Token;
use Modules\User\Enums\ProfileTypeEnum;
use Modules\User\Enums\UserLevelEnum;

class User extends Authenticatable
{
    use HasApiTokens, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'phone',
        'password',
        'level',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'blocked_at' => 'datetime',
            'blocked' => 'boolean',
            'level' => UserLevelEnum::class,
        ];
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    public function getUserDefaultProfile(): ?Profile
    {
        return $this->profiles()->where('type', ProfileTypeEnum::PRIMARY->value)->firstOrFail();
    }

    public function tokenable(): MorphOne
    {
        return $this->morphOne(Token::class, 'tokenable');
    }
}
