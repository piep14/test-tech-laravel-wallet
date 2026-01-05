<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\RecurringTransferResource;
use App\Models\RecurringTransfer;

class RecurringTransferController
{
    public function index()
    {
        $recurringTransfers = RecurringTransfer::query()
            ->where('user_id', auth()->id())
            ->get();

        return RecurringTransferResource::collection($recurringTransfers);
    }
}
