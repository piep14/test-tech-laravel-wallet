<?php

namespace App\Console\Commands;

use App\Actions\PerformWalletTransfer;
use App\Models\RecurringTransfer as RecurringTransferModel;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class RecurringTransfer extends Command
{
    protected $signature = 'transfer:recurring';

    protected $description = 'Transfer money to wallet';

    public function handle(): void
    {
        $today = now()->startOfDay();

        $recurringTransfers = RecurringTransferModel::query()
            ->whereDate('start_date', '<=', $today)
            ->where(function ($query) use ($today) {
                $query
                    ->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $today);
            })
            ->get();

        foreach ($recurringTransfers as $recurringTransfer) {
            $lastTransfer = $recurringTransfer
                ->user
                ->wallet
                ->transactions()
                ->where('reason', $recurringTransfer->reason)
                ->latest()
                ->first();

            $nextExecutionDate = $lastTransfer
                ? $lastTransfer->created_at->addDays($recurringTransfer->frequency_days)
                : $recurringTransfer->start_date;

            if (Carbon::parse($nextExecutionDate)->lte($today)) {
                dump('Transferring money to wallet...');

                app(PerformWalletTransfer::class)
                    ->execute(
                        $recurringTransfer->user,
                        $recurringTransfer->recipient,
                        $recurringTransfer->amount,
                        $recurringTransfer->reason
                    );
            }
        }
    }
}