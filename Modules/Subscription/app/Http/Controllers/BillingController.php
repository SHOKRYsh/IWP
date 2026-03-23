<?php

namespace Modules\Subscription\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Subscription\Models\Billing;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('sanctum')->user();

        $query = Billing::with(['subscription.plan']);
        if (!$user->hasRole('Admin')) {
            $query->where('user_id',$user->id);
        } else {
            if ($request->user_id) {
                $query->where('user_id',$request->user_id);
            }
            if ($request->plan_id) {
                $query->whereHas('subscription', function ($query) use ($request) {
                    $query->where('plan_id',$request->plan_id);
                });
            }
            if ($request->status) {
                $query->where('status',$request->status);
            }
        }

        $billings = $query->latest()->paginate();
        return $this->respondOk($billings, 'Billing history retrieved successfully');
    }
}
