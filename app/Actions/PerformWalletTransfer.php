<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\WalletTransactionType;
use App\Exceptions\InsufficientBalance;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransfer;
use Illuminate\Support\Facades\DB;

readonly class PerformWalletTransfer
{
    public function __construct(protected PerformWalletTransaction $performWalletTransaction)
    {
    }

    /**
     * @throws InsufficientBalance
     */
    public function execute(User $sender, User $recipient, int $amount, string $reason): WalletTransfer
    {
        return DB::transaction(function () use ($sender, $recipient, $amount, $reason) {
            $walletId = $recipient->wallet->id ?? null;

            if (!$recipient->wallet) {
                $walletId = Wallet::create([
                    'user_id' => $recipient->id,
                    'balance' => 0
                ])?->id;

                $recipient->refresh();
            }

            $transfer = WalletTransfer::create([
                'amount' => $amount,
                'source_id' => $sender->wallet->id,
                'target_id' => $walletId,
            ]);

            $this->performWalletTransaction->execute(
                wallet: $sender->wallet,
                type: WalletTransactionType::DEBIT,
                amount: $amount,
                reason: $reason,
                transfer: $transfer
            );

            $this->performWalletTransaction->execute(
                wallet: $recipient->wallet,
                type: WalletTransactionType::CREDIT,
                amount: $amount,
                reason: $reason,
                transfer: $transfer
            );

            return $transfer;
        });
    }
}
