<?php

namespace App\Listeners;

use App\Events\PaymentReceived;

class UpdateOrderStatus
{
    public function handle(PaymentReceived $event): void
    {
        $event->payment->order?->update(['status' => 'paid']);
    }
}
