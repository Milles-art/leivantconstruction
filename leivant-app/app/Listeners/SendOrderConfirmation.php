<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Support\Facades\Log;

class SendOrderConfirmation
{
    public function handle(OrderPlaced $event): void
    {
        Log::info('Order confirmation prepared.', [
            'order_number' => $event->order->order_number,
            'customer_email' => $event->order->customer_email,
        ]);
    }
}
