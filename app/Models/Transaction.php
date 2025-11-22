<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'type',
        'amount',
        'status',
        'description',
        'reversed_by_transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function reversedBy(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'reversed_by_transaction_id');
    }

    public function isReversed(): bool
    {
        return $this->status === 'reversed';
    }

    public function canBeReversed(): bool
    {
        return $this->status === 'completed' && !$this->reversed_by_transaction_id;
    }
}
