<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\DuitkuService;
use App\Services\PaymentTransactionService;
use App\Services\PricingCatalogService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DuitkuWebhookController extends Controller
{
    public function handle(
        Request $request,
        DuitkuService $duitkuService,
        SubscriptionService $subscriptionService,
        PricingCatalogService $pricingCatalogService,
        PaymentTransactionService $paymentTransactionService
    )
    {
        $payload = $request->all();

        Log::info('Duitku webhook received', ['payload' => $payload]);

        $merchantOrderId = (string) $request->input('merchantOrderId');
        $amount = (string) $request->input('amount');
        $signature = (string) $request->input('signature');
        $resultCode = (string) $request->input('resultCode');
        $reference = (string) $request->input('reference');

        if (!$duitkuService->isValidCallbackSignature($amount, $merchantOrderId, $signature)) {
            Log::warning('Duitku callback signature mismatch', ['merchantOrderId' => $merchantOrderId]);

            return response()->json(['message' => 'Invalid signature'], 400);
        }

        if ($resultCode !== '00') {
            return response()->json(['message' => 'Ignored'], 200);
        }

        $parsed = $duitkuService->parseMerchantOrderId($merchantOrderId);
        $userId = $parsed['user_id'] ?? null;
        $type = $parsed['type'] ?? null;

        if (!$userId || !$type) {
            return response()->json(['message' => 'Invalid order id'], 400);
        }

        $owner = User::find($userId);
        if (!$owner) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $plans = $pricingCatalogService->getPlanDetails();
        $paymentTransactionService->syncFromCallback($owner, $type, $payload);

        if ($type === 'topup') {
            $quota = $subscriptionService->addQuota(
                $owner,
                (int) data_get($plans, 'topup.quota', 0),
                $merchantOrderId,
                'duitku',
                $reference,
            );
            $paymentTransactionService->attachBillable($merchantOrderId, $quota);
        } else {
            $planKey = strtolower($type);
            if (!in_array($planKey, ['pro', 'business'], true)) {
                return response()->json(['message' => 'Unsupported plan'], 400);
            }

            $subscription = $subscriptionService->activateSubscription(
                $owner,
                $planKey,
                $merchantOrderId,
                null,
                1,
                'duitku',
                $reference,
            );
            $paymentTransactionService->attachBillable($merchantOrderId, $subscription);
        }

        return response()->json(['message' => 'OK'], 200);
    }
}
