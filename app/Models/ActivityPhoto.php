<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('activity_photos', keyType: 'string', incrementing: false, timestamps: false)]
#[Fillable(['id', 'session_id', 'event_id', 'file_path', 'thumbnail_path', 'filename', 'latitude', 'longitude', 'timestamp', 'selected'])]
class ActivityPhoto extends Model
{
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'timestamp' => 'datetime',
            'selected' => 'boolean',
        ];
    }

    public function session()
    {
        return $this->belongsTo(
            TrackingSession::class,
            'session_id'
        );
    }

    public function event()
    {
        return $this->belongsTo(
            ActivityEvent::class,
            'event_id'
        );
    }
}
