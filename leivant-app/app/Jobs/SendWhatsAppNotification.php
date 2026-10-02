<?php

namespace App\Jobs;

use App\Models\Inquiry;
use App\Models\Order;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendWhatsAppNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order|Inquiry $notifiable)
    {
    }

    public function handle(WhatsAppService $whatsAppService): void
    {
        if ($this->notifiable instanceof Order) {
            $whatsAppService->sendOrderConfirmation($this->notifiable);

            return;
        }

        $whatsAppService->sendInquiryNotification($this->notifiable);
    }
}
