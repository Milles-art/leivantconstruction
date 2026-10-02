<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;

class MpesaService
{
    public function __construct(private readonly AzamPayService $azamPayService)
    {
    }

    public function stkPush(Order $order, string $phone): Payment
    {
        return $this->azamPayService->stkPush($order, $phone, 'mpesa');
    }
}
