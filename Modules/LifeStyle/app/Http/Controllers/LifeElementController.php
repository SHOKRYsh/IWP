<?php

namespace Modules\LifeStyle\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\LifeStyle\Models\LifeElement;
use Modules\LifeStyle\Models\LifeTaskType;

class LifeElementController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('sanctum')->user();

        $query = LifeElement::with(['lifeStyle', 'user', 'taskTypes' => function ($q) use ($user) {
            if (!$user->hasRole('Admin')) {
                $q->where('user_id', $user->id)
                    ->orWhereNull('user_id')
                    ->orWhereHas('user', function ($u) {
                        $u->hasRole('Admin');
                    });
            }
        }]);

        if (!$user->hasRole('Admin')) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereNull('user_id')
                    ->orWhereHas('user', function ($u) {
                        $u->hasRole('Admin');
                    });
            });
        }

        if ($request->has('life_style_id')) {
            $query->where('life_style_id', $request->life_style_id);
        }

        $elements = $query->paginate();

        return $this->respondOk($elements, 'LifeElements retrieved successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'life_style_id' => 'required|exists:life_styles,id',
            'name' => 'required|string|max:255',
            'suggested_tasks' => 'nullable|array',
            'icon_key' => 'nullable|string|max:255',
        ]);

        $user = auth('sanctum')->user();
        $validated['user_id'] = $user->id;

        $element = LifeElement::create($validated);

        if (!$user->hasRole('Admin')) {
            $user->lifeElements()->sync($element->id);
        }

        return $this->respondCreated($element, 'LifeElement created successfully');
    }

    public function show($id)
    {
        $user = auth('sanctum')->user();
        $element = LifeElement::with(['lifeStyle', 'user', 'taskTypes' => function ($q) use ($user) {
            if (!$user->hasRole('Admin')) {
                $q->where('user_id', $user->id)
                    ->orWhereNull('user_id')
                    ->orWhereHas('user', function ($u) {
                        $u->hasRole('Admin');
                    });
            }
        }])->findOrFail($id);

        return $this->respondOk($element, 'LifeElement retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        $element = LifeElement::findOrFail($id);

        if (!$user->hasRole('Admin') && $element->user_id !== $user->id) {
            return $this->respondNotFound(null, 'Unauthorized to update this life element');
        }

        $validated = $request->validate([
            'life_style_id' => 'sometimes|required|exists:life_styles,id',
            'name' => 'sometimes|required|string|max:255',
            'suggested_tasks' => 'nullable|array',
            'icon_key' => 'sometimes|required|string|max:255',
        ]);

        $element->update($validated);

        return $this->respondOk($element, 'LifeElement updated successfully');
    }

    public function destroy($id)
    {
        $user = auth('sanctum')->user();
        $element = LifeElement::findOrFail($id);

        if (!$user->hasRole('Admin') && $element->user_id !== $user->id) {
            return $this->respondNotFound(null, 'Unauthorized to delete this life element');
        }

        $element->delete();

        return $this->respondOk(null, 'LifeElement deleted successfully');
    }

    //---------------------------------------------------------------------------------------------------------

    public function storeTaskType(Request $request, $element_id)
    {
        $user = auth('sanctum')->user();
        $element = LifeElement::findOrFail($element_id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon_key' => 'nullable|string|max:255',
        ]);

        $taskType = $element->taskTypes()->create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'icon_key' => $validated['icon_key'],
        ]);

        return $this->respondCreated($taskType, 'LifeTaskType created successfully');
    }

    public function updateTaskType(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        $taskType = LifeTaskType::findOrFail($id);

        if (!$user->hasRole('Admin') && $taskType->user_id !== $user->id) {
            return $this->respondNotFound(null, 'Unauthorized to update this task type');
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'icon_key' => 'sometimes|required|string|max:255',
        ]);

        $taskType->update($validated);

        return $this->respondOk($taskType, 'LifeTaskType updated successfully');
    }

    public function destroyTaskType($id)
    {
        $user = auth('sanctum')->user();
        $taskType = LifeTaskType::findOrFail($id);

        if (!$user->hasRole('Admin') && $taskType->user_id !== $user->id) {
            return $this->respondNotFound(null, 'Unauthorized to delete this task type');
        }

        $taskType->delete();

        return $this->respondOk(null, 'LifeTaskType deleted successfully');
    }
}
