<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function notification(Request $request, MidtransService $midtransService)
    {
        $notification = $request->all();
        
        // Verify signature (optional but recommended)
        // For simplicity, we trust Midtrans server notification
        
        $midtransService->handleNotification($notification);

        return response()->json(['status' => 'ok']);
    }
}
