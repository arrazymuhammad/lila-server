<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('activity_events', keyType: 'string', incrementing: false, timestamps: false)]
#[Fillable(['id', 'session_id', 'title', 'description', 'latitude', 'longitude', 'timestamp', 'status'])]
class ActivityEvent extends Model
{

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'timestamp' => 'datetime',
        ];
    }

    public function session()
    {
        return $this->belongsTo(
            TrackingSession::class,
            'session_id'
        );
    }

    public function photos()
    {
        return $this->hasMany(
            ActivityPhoto::class,
            'event_id'
        );
    }
}
