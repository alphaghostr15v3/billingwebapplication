<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::all();
        return view('super-admin.subscriptions.index', compact('subscriptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plan_name' => 'required',
            'price' => 'required|numeric',
            'duration_days' => 'required|integer',
        ]);

        Subscription::create($request->all());

        return redirect()->back()->with('success', 'Subscription plan created!');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return redirect()->back()->with('success', 'Subscription plan deleted!');
    }
}
