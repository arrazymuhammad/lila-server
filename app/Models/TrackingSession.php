<?php

namespace App\Models;

use App\Models\TrackPoint;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[Table('tracking_sessions', keyType: 'string', incrementing: false, timestamps: false)]
#[Fillable(['id', 'user_id', 'title', 'start_time', 'end_time', 'distance', 'duration_seconds', 'status', 'rejected_reason'])]
class TrackingSession extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'distance' => 'float',
            'duration_seconds' => 'integer',
        ];
    }

    public function trackPoints()
    {
        return $this->hasMany(TrackPoint::class, 'session_id');
    }

    public function events()
    {
        return $this->hasMany(ActivityEvent::class, 'session_id');
    }

    public function photos()
    {
        return $this->hasMany(ActivityPhoto::class, 'session_id');
    }
}
