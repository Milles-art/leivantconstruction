<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AzamPayService
{
    public function stkPush(Order $order, string $phone, string $channel): Payment
    {
        $payment = $order->payment()->create([
            'provider' => 'azam_pay',
            'channel' => $channel,
            'reference' => 'AZAM-'.Str::upper(Str::random(12)),
            'external_reference' => $order->order_number,
            'amount' => $order->total,
            'status' => 'pending',
            'payload' => ['phone' => $this->normalizePhone($phone)],
        ]);

        if (config('services.azam_pay.mock')) {
            $payment->update([
                'status' => 'success',
                'paid_at' => now(),
                'payload' => array_merge($payment->payload ?? [], [
                    'mock' => true,
                    'message' => 'Sandbox payment accepted locally.',
                    'transactionId' => $payment->reference,
                ]),
            ]);

            return $payment->refresh();
        }

        $response = $this->client()->post('/azampay/mno/checkout', [
            'accountNumber' => $this->normalizePhone($phone),
            'amount' => (string) $order->total,
            'currency' => 'TZS',
            'externalId' => $order->order_number,
            'provider' => $this->providerName($channel),
            'additionalProperties' => [
                'order_number' => $order->order_number,
                'customer_phone' => $phone,
            ],
        ])->throw()->json();

        $payment->update([
            'reference' => $response['transactionId'] ?? $payment->reference,
            'payload' => $response,
            'status' => ($response['success'] ?? false) ? 'pending' : 'failed',
        ]);

        return $payment->refresh();
    }

    public function checkStatus(Payment $payment): array
    {
        if (config('services.azam_pay.mock')) {
            return [
                'status' => $payment->status,
                'reference' => $payment->reference,
                'mock' => true,
            ];
        }

        return $this->client()->post('/azampay/transactionstatus', [
            'transactionId' => $payment->reference,
            'externalId' => $payment->external_reference,
        ])->throw()->json();
    }

    public function handleCallback(array $payload): ?Payment
    {
        $reference = $payload['externalreference']
            ?? $payload['externalReference']
            ?? $payload['reference']
            ?? null;

        if (! $reference) {
            return null;
        }

        $payment = Payment::query()
            ->where('external_reference', $reference)
            ->orWhere('reference', $reference)
            ->first();

        if (! $payment) {
            return null;
        }

        $status = str($payload['transactionstatus'] ?? $payload['status'] ?? 'pending')->lower()->toString();

        $payment->update([
            'status' => $status === 'success' ? 'success' : ($status === 'failed' ? 'failed' : 'pending'),
            'payload' => $payload,
            'paid_at' => $status === 'success' ? now() : $payment->paid_at,
        ]);

        return $payment->refresh();
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl(rtrim((string) config('services.azam_pay.base_url'), '/'))
            ->acceptJson()
            ->asJson()
            ->withToken($this->accessToken())
            ->withHeaders([
                'X-API-Key' => config('services.azam_pay.client_id'),
            ])
            ->timeout(30);
    }

    private function accessToken(): string
    {
        if (! config('services.azam_pay.app_name') || ! config('services.azam_pay.client_id') || ! config('services.azam_pay.client_secret')) {
            throw new \RuntimeException('AzamPay live credentials are not configured.');
        }

        $response = Http::baseUrl(rtrim((string) config('services.azam_pay.base_url'), '/'))
            ->acceptJson()
            ->asJson()
            ->post('/AppRegistration/GenerateToken', [
                'appName' => config('services.azam_pay.app_name'),
                'clientId' => config('services.azam_pay.client_id'),
                'clientSecret' => config('services.azam_pay.client_secret'),
            ])
            ->throw()
            ->json();

        return $response['data']['accessToken']
            ?? $response['accessToken']
            ?? $response['token']
            ?? '';
    }

    private function providerName(string $channel): string
    {
        return match ($channel) {
            'mpesa' => 'Mpesa',
            'tigopesa' => 'Tigo',
            'airtel' => 'Airtel',
            'halopesa' => 'Halopesa',
            default => $channel,
        };
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?: '';

        if (str_starts_with($digits, '0')) {
            return '255'.substr($digits, 1);
        }

        return $digits;
    }
}
