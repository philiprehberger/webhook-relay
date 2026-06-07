<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryAttempt extends Model
{
    use HasUlids;

    protected $fillable = [
        'delivery_id',
        'attempt_number',
        'request_signature',
        'response_status',
        'response_headers',
        'response_body_snippet',
        'latency_ms',
        'error_code',
        'attempted_at',
    ];

    protected function casts(): array
    {
        return [
            'attempt_number' => 'integer',
            'response_status' => 'integer',
            'response_headers' => 'array',
            'latency_ms' => 'integer',
            'attempted_at' => 'datetime',
        ];
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }
}
