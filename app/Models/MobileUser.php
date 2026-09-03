<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[Table('mobile_users', keyType: 'string', incrementing: false)]
#[Fillable(['id', 'name', 'phone', 'email', 'pin', 'auth_token', 'last_login_at', 'is_active'])]
class MobileUser extends Model
{
    use HasUuids;

    protected $hidden = [
        'pin',
        'auth_token',
    ];

    protected function casts(): array
    {
        return [
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function sessions()
    {
        return $this->hasMany(TrackingSession::class, 'mobile_user_id');
    }

    public function events()
    {
        return $this->hasMany(ActivityEvent::class, 'mobile_user_id');
    }
}
