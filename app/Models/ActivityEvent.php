<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('activity_events', keyType: 'string', incrementing: false, timestamps: false)]
#[Fillable(['id', 'session_id', 'mobile_user_id', 'title', 'description', 'latitude', 'longitude', 'timestamp', 'status','operator_category', 'voice_note_path', 'voice_note_duration_seconds', 'voice_note_transcription', 'transcribed_by'])]
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

    public function mobileUser()
    {
        return $this->belongsTo(MobileUser::class, 'mobile_user_id');
    }

    public function transcribedBy()
    {
        return $this->belongsTo(User::class, 'transcribed_by');
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
