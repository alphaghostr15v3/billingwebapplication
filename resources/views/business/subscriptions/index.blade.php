@extends('layouts.business')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold">My Subscription</h2>
    <p class="text-muted">Manage your plan and billing details.</p>
</div>

@if($currentSubscription->subscription_status === 'active')
<div class="card p-4 border-0 shadow-sm mb-4 bg-primary bg-opacity-10">
    <div class="d-flex align-items-center">
        <div class="bg-primary text-white p-3 rounded-circle me-3">
            <i class="fas fa-crown fs-4"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-1">Active Plan: {{ $currentSubscription->subscription->plan_name }}</h5>
            <p class="mb-0 text-muted">Your subscription expires on {{ \Carbon\Carbon::parse($currentSubscription->subscription_expires_at)->format('d M Y') }}</p>
        </div>
    </div>
</div>
@else
<div class="alert alert-warning border-0 shadow-sm mb-4">
    <i class="fas fa-exclamation-triangle me-2"></i> You are currently on a <strong>Trial Plan</strong>. Upgrade now to avoid service interruption!
</div>
@endif

<div class="row g-4">
    @foreach($plans as $plan)
    <div class="col-md-4">
        <div class="card h-100 p-4 border-0 shadow-sm {{ $currentSubscription->subscription_id == $plan->id ? 'border-primary border-2' : '' }}">
            @if($currentSubscription->subscription_id == $plan->id)
                <span class="badge bg-primary position-absolute top-0 start-50 translate-middle">Current Plan</span>
            @endif
            <h4 class="fw-bold mb-1">{{ $plan->plan_name }}</h4>
            <div class="mb-4">
                <span class="display-6 fw-bold">₹{{ number_format($plan->price, 0) }}</span>
                <span class="text-muted">/ {{ $plan->duration_days }} Days</span>
            </div>
            <ul class="list-unstyled mb-4 flex-grow-1">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Unlimited Invoices</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Multi-Device Support</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> GST & Tax Reports</li>
            </ul>
            <button class="btn {{ $currentSubscription->subscription_id == $plan->id ? 'btn-outline-primary' : 'btn-primary' }} w-100 py-2 subscribe-btn" 
                    data-plan-id="{{ $plan->id }}" 
                    data-plan-name="{{ $plan->plan_name }}" 
                    data-price="{{ $plan->price }}"
                    {{ $currentSubscription->subscription_id == $plan->id ? 'disabled' : '' }}>
                {{ $currentSubscription->subscription_id == $plan->id ? 'Current Plan' : 'Upgrade Now' }}
            </button>
        </div>
    </div>
    @endforeach
</div>
@endsection

@section('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    $(document).ready(function() {
        $('.subscribe-btn').click(function() {
            const planId = $(this).data('plan-id');
            const planName = $(this).data('plan-name');
            const btn = $(this);
            
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');

            $.ajax({
                url: "{{ route('business.subscriptions.initiate') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    plan_id: planId
                },
                success: function(response) {
                    var options = {
                        "key": response.razorpay_id,
                        "amount": response.amount,
                        "currency": "INR",
                        "name": "Billing Application",
                        "description": "Subscription for " + planName,
                        "order_id": response.order_id,
                        "handler": function (payResponse){
                            $.ajax({
                                url: "{{ route('business.subscriptions.verify') }}",
                                type: 'POST',
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    razorpay_payment_id: payResponse.razorpay_payment_id,
                                    razorpay_order_id: payResponse.razorpay_order_id,
                                    razorpay_signature: payResponse.razorpay_signature,
                                    plan_id: planId
                                },
                                success: function(verifyResponse) {
                                    if(verifyResponse.status === 'success') {
                                        Swal.fire('Success!', verifyResponse.message, 'success')
                                            .then(() => location.reload());
                                    } else {
                                        Swal.fire('Error', verifyResponse.message, 'error');
                                        btn.prop('disabled', false).html('Upgrade Now');
                                    }
                                }
                            });
                        },
                        "prefill": {
                            "name": response.name,
                            "email": response.email
                        },
                        "theme": {
                            "color": "#4f46e5"
                        }
                    };
                    var rzp1 = new Razorpay(options);
                    rzp1.open();
                },
                error: function() {
                    Swal.fire('Error', 'Something went wrong', 'error');
                    btn.prop('disabled', false).html('Upgrade Now');
                }
            });
        });
    });
</script>
@endsection
