<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\RecurringTransfer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

/**
 * @mixin RecurringTransfer
 */
class RecurringTransferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'recipient_id' => $this->recipient_id,
            'recipient' => $this->recipient, // Resource à ajouter pour ne retourner que les champs utiles
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'frequency_days' => $this->frequency_days,
            'amount' => $this->amount,
            'amount_formatted' => Number::currency($this->amount / 100, 'EUR', 'fr'),
            'reason' => $this->reason,
        ];
    }
}
