<?php

namespace Modules\Subscription\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Subscription\Models\Plan;
use Modules\Subscription\Models\Subscription;

class SubscriptionController extends Controller
{
    public function getAllSubscriptions(Request $request)
    {
        $user = auth('sanctum')->user();
        if(!$user->hasRole('Admin'))
        {
            $subscriptions = $user->subscriptions()->latest()->get();
        }
        else
        {
            $query = Subscription::with('user');

            if($request->has('user_id'))
            {
                $query->where('user_id', $request->user_id);
            }

            if($request->has('type'))
            {
                $query->where('type', $request->type);
            }

            if($request->has('plan_id'))
            {
                $query->where('plan_id', $request->plan_id);
            }

            $subscriptions = $query->latest()->get();
        }

        return $this->respondOk($subscriptions, 'Subscriptions retrieved successfully');
    }

    public function status()
    {
        $user = auth('sanctum')->user();
        $subscription = $user->subscriptions()->latest()->first();

        if (!$subscription) {
            return $this->respondNotFound(null, 'No subscription found');
        }

        return $this->respondOk([
            'type' => $subscription->type,
            'started_at' => $subscription->started_at?->toDateTimeString(),
            'ends_at' => $subscription->ends_at?->toDateTimeString(),
            'is_active' => $subscription->isActive(),
        ], 'Subscription status retrieved successfully');
    }

    //--------------------------------------------------------------------------------------------------
    
    public function adminSubscription(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        $subscription = Subscription::create([
            'user_id' => $request->user_id,
            'plan_id' => $plan->id,
            'type' => 'admin',
            'started_at' => now(),
            'ends_at' => now()->addDays($plan->duration),
        ]);

        return $this->respondOk($subscription, 'Subscription created successfully');
    }
}
