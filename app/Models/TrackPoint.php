<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('track_points', timestamps: false)]
#[Fillable(['session_id', 'latitude', 'longitude', 'timestamp'])]
class TrackPoint extends Model
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
}
