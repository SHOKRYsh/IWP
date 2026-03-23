<?php

namespace Modules\Subscription\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Subscription\Models\Plan;

class PlanController extends Controller
{
    public function index()
    {
        $user = auth('sanctum')->user();
        $query = Plan::query();

        if(!$user->hasRole('Admin'))
        {
            $query->where('is_active', true);
        }

        $plans = $query->paginate();
        return $this->respondOk($plans, 'Plans retrieved successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        $plan = Plan::create($validated);
        return $this->respondCreated($plan, 'Plan created successfully');
    }

    public function show($id)
    {
        $plan = Plan::findOrFail($id);
        return $this->respondOk($plan, 'Plan retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'duration' => 'sometimes|required|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        $plan->update($validated);
        return $this->respondOk($plan, 'Plan updated successfully');
    }

    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();
        return $this->respondOk(null, 'Plan deleted successfully');
    }
}
