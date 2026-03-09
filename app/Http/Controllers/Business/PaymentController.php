<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Business;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    private $razorpayId = "rzp_test_dummy"; // Should come from .env
    private $razorpayKey = "dummy_secret"; // Should come from .env

    public function index()
    {
        $plans = Subscription::all();
        $currentSubscription = auth()->user()->business;
        return view('business.subscriptions.index', compact('plans', 'currentSubscription'));
    }

    public function initiatePayment(Request $request)
    {
        $plan = Subscription::findOrFail($request->plan_id);
        
        $api = new Api($this->razorpayId, $this->razorpayKey);

        $orderData = [
            'receipt'         => 'rcpt_' . auth()->user()->id . '_' . time(),
            'amount'          => $plan->price * 100, // In paise
            'currency'        => 'INR',
            'payment_capture' => 1
        ];

        $razorpayOrder = $api->order->create($orderData);

        return response()->json([
            'order_id' => $razorpayOrder['id'],
            'amount' => $orderData['amount'],
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'plan_id' => $plan->id,
            'razorpay_id' => $this->razorpayId
        ]);
    }

    public function verifyPayment(Request $request)
    {
        $success = true;
        $error = "Payment Failed";

        if (empty($request->razorpay_payment_id) === false) {
            $api = new Api($this->razorpayId, $this->razorpayKey);

            try {
                $attributes = array(
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature' => $request->razorpay_signature
                );

                $api->utility->verifyPaymentSignature($attributes);
            } catch (\Exception $e) {
                $success = false;
                $error = 'Razorpay Error : ' . $e->getMessage();
            }
        }

        if ($success === true) {
            $business = auth()->user()->business;
            $plan = Subscription::find($request->plan_id);

            $business->update([
                'subscription_id' => $plan->id,
                'subscription_status' => 'active',
                'subscription_expires_at' => now()->addDays($plan->duration_days)
            ]);

            return response()->json(['status' => 'success', 'message' => 'Payment successful!']);
        }

        return response()->json(['status' => 'error', 'message' => $error]);
    }
}
