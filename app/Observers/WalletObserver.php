<?php

namespace App\Observers;

use App\Models\Wallet;
use App\Notifications\WalletUnder10EuroNotification;

class WalletObserver
{
    public function updated(Wallet $wallet)
    {
        if ($wallet->wasChanged('balance') && $wallet->balance < 1000) {
            $wallet->user->notify(new WalletUnder10EuroNotification($wallet->balance));
        }
    }
}
