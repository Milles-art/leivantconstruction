<?php

namespace App\Services;

use App\Models\Inquiry;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function sendOrderConfirmation(Order $order): bool
    {
        return $this->sendText(
            config('services.whatsapp.owner_number'),
            "New Leivant order {$order->order_number}: TZS ".number_format($order->total)." for {$order->customer_name} ({$order->customer_phone})."
        );
    }

    public function sendInquiryNotification(Inquiry $inquiry): bool
    {
        $location = $inquiry->region
            ? " in {$inquiry->region}".($inquiry->site_location ? " ({$inquiry->site_location})" : '')
            : '';
        $scope = $inquiry->project_type ? " Project: {$inquiry->project_type}." : '';
        $timeline = $inquiry->timeline ? " Timeline: {$inquiry->timeline}." : '';

        return $this->sendText(
            config('services.whatsapp.owner_number'),
            "New Leivant inquiry from {$inquiry->name} ({$inquiry->phone}){$location}: {$inquiry->subject}.{$scope}{$timeline}"
        );
    }

    public function sendText(?string $to, string $message): bool
    {
        $token = config('services.whatsapp.token');
        $phoneNumberId = config('services.whatsapp.phone_number_id');

        if (! $token || ! $phoneNumberId || ! $to) {
            Log::info('WhatsApp notification skipped; credentials are not configured.', [
                'to' => $to,
                'message' => $message,
            ]);

            return false;
        }

        Http::withToken($token)
            ->acceptJson()
            ->asJson()
            ->post("https://graph.facebook.com/v20.0/{$phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'text',
                'text' => ['body' => $message],
            ])
            ->throw();

        return true;
    }
}
