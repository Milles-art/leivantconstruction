<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use Illuminate\Support\Facades\Log;

class PaymentSuccessfulNotification
{
    public function handle(PaymentReceived $event): void
    {
        Log::info('Payment successful notification prepared.', [
            'payment_id' => $event->payment->id,
            'order_id' => $event->payment->order_id,
        ]);
    }
}
