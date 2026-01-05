<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecurringTransfer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'source_id',
        'target_id',
        'amount',
        'start_date',
        'end_date',
        'reason'
    ];

    /**
     * @return BelongsTo<Wallet>
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Wallet>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
