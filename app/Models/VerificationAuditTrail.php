<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationAuditTrail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'event_id',
        'action',
        'verifier_name',
        'reason',
        'changes',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function session()
    {
        return $this->belongsTo(TrackingSession::class, 'session_id');
    }
}
