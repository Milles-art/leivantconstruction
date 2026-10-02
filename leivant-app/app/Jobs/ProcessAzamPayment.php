<?php

namespace App\Jobs;

use App\Events\PaymentReceived;
use App\Models\Payment;
use App\Services\AzamPayService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessAzamPayment implements ShouldQueue
{
    use Queueable;

    public function __construct(public Payment $payment)
    {
    }

    public function handle(AzamPayService $azamPayService): void
    {
        $status = $azamPayService->checkStatus($this->payment);

        if (($status['status'] ?? null) === 'success') {
            $this->payment->update([
                'status' => 'success',
                'paid_at' => $this->payment->paid_at ?? now(),
                'payload' => array_merge($this->payment->payload ?? [], ['status_check' => $status]),
            ]);

            PaymentReceived::dispatch($this->payment->refresh());
        }
    }
}
