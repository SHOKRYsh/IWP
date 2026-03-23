<?php

namespace Modules\Subscription\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\ResponsesTrait;

class CheckSubscription
{
    use ResponsesTrait;
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('api/auth/*') || $request->is('api/run-seeder') || $request->is('api/subscription/status') || $request->is('api/plan/*') || $request->is('api/subscription/billing/history')) {
            return $next($request);
        }

        $user = auth('sanctum')->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->hasRole('Admin')) {
            return $next($request);
        }

        $subscription = $user->subscriptions()->latest()->first();

        if (!$subscription || !$subscription->isActive()) {
            return $this->respondError(null,'Your trial or subscription has expired. Please subscribe to continue.');
        }

        return $next($request);
    }
}
