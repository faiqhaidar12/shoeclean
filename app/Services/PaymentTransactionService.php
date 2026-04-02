<?php

namespace App\Services;

use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PaymentTransactionService
{
    public function recordCheckout(User $owner, string $plan, array $planDetails, array $checkoutPayload): PaymentTransaction
    {
        $merchantOrderId = (string) data_get($checkoutPayload, 'merchantOrderId');

        return PaymentTransaction::updateOrCreate(
            ['merchant_order_id' => $merchantOrderId],
            [
                'user_id' => $owner->id,
                'gateway' => 'duitku',
                'kind' => $plan === 'topup' ? 'topup' : 'subscription',
                'plan_key' => $plan,
                'reference' => data_get($checkoutPayload, 'reference'),
                'payment_method' => data_get($checkoutPayload, 'paymentMethod'),
                'amount' => (int) ($planDetails['price'] ?? 0),
                'status_code' => data_get($checkoutPayload, 'statusCode'),
                'status_message' => data_get($checkoutPayload, 'statusMessage'),
                'product_detail' => data_get($checkoutPayload, 'productDetails'),
                'customer_email' => $owner->email,
                'checkout_payload' => $checkoutPayload,
                'expires_at' => now()->addMinutes((int) config('duitku.expiry_period', 60)),
                'last_synced_at' => now(),
            ]
        );
    }

    public function syncFromCallback(User $owner, string $plan, array $callbackPayload): PaymentTransaction
    {
        $merchantOrderId = (string) data_get($callbackPayload, 'merchantOrderId');
        $resultCode = (string) data_get($callbackPayload, 'resultCode');

        return PaymentTransaction::updateOrCreate(
            ['merchant_order_id' => $merchantOrderId],
            [
                'user_id' => $owner->id,
                'gateway' => 'duitku',
                'kind' => $plan === 'topup' ? 'topup' : 'subscription',
                'plan_key' => $plan,
                'reference' => data_get($callbackPayload, 'reference'),
                'payment_method' => data_get($callbackPayload, 'paymentCode'),
                'amount' => (int) data_get($callbackPayload, 'amount', 0),
                'status_code' => $resultCode,
                'result_code' => $resultCode,
                'status_message' => $resultCode === '00' ? 'SUCCESS' : 'FAILED',
                'product_detail' => data_get($callbackPayload, 'productDetail'),
                'customer_email' => data_get($callbackPayload, 'merchantUserId', $owner->email),
                'callback_payload' => $callbackPayload,
                'paid_at' => $resultCode === '00' ? now() : null,
                'last_synced_at' => now(),
            ]
        );
    }

    public function syncFromStatus(User $owner, string $plan, string $merchantOrderId, array $statusPayload): PaymentTransaction
    {
        $statusCode = (string) data_get($statusPayload, 'statusCode');

        return PaymentTransaction::updateOrCreate(
            ['merchant_order_id' => $merchantOrderId],
            [
                'user_id' => $owner->id,
                'gateway' => 'duitku',
                'kind' => $plan === 'topup' ? 'topup' : 'subscription',
                'plan_key' => $plan,
                'reference' => data_get($statusPayload, 'reference'),
                'amount' => (int) data_get($statusPayload, 'amount', 0),
                'fee' => data_get($statusPayload, 'fee'),
                'status_code' => $statusCode,
                'status_message' => data_get($statusPayload, 'statusMessage'),
                'status_payload' => $statusPayload,
                'paid_at' => $statusCode === '00' ? now() : null,
                'last_synced_at' => now(),
            ]
        );
    }

    public function attachBillable(string $merchantOrderId, Model $billable): void
    {
        PaymentTransaction::query()
            ->where('merchant_order_id', $merchantOrderId)
            ->update([
                'billable_type' => $billable::class,
                'billable_id' => $billable->getKey(),
            ]);
    }
}
