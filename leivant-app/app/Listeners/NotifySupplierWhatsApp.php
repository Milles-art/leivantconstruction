<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Jobs\SendWhatsAppNotification;

class NotifySupplierWhatsApp
{
    public function handle(OrderPlaced $event): void
    {
        SendWhatsAppNotification::dispatch($event->order);
    }
}
