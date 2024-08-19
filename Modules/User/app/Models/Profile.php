<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Shared\Models\Observers\BlamableTrait;

class Profile extends Model
{
    use BlamableTrait, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'user_name',
        'full_name',
        'description',
        'email',
        'phone',
        'display_img',
        'cover_img',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'blocked' => 'boolean',
            'blocked_at' => 'datetime',
            'verified' => 'boolean',
            'verified_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
        ];
    }
}
