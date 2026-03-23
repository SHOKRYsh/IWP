<?php

namespace Modules\Children\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Modules\Children\Models\Child;
use Illuminate\Support\Facades\Auth;

class ChildrenController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('sanctum')->user();
        $query = Child::with('user:id,name,phone,email,profile_image');

        if (!$user->hasRole('Admin')) {
            $query->where('user_id', $user->id);
        } else {
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        }

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $children = $query->paginate();

        return $this->respondOk($children, 'Children retrieved successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:male,female',
            'educational_stage' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0|max:100',
            'extracurricular_activities' => 'nullable|boolean',
            'ballet_class' => 'nullable|string|max:255',
        ]);

        $child = Auth::user()->children()->create($validated);

        return $this->respondCreated($child, 'Child added successfully');
    }
    
    public function show($id)
    {
        $child = Auth::user()->children()->findOrFail($id);
        return $this->respondOk($child, 'Child retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $child = Auth::user()->children()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'gender' => 'nullable|in:male,female',
            'educational_stage' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0|max:100',
            'extracurricular_activities' => 'nullable|boolean',
            'ballet_class' => 'nullable|string|max:255',
        ]);

        $child->update($validated);

        return $this->respondOk($child, 'Child updated successfully');
    }

    public function destroy($id)
    {
        $child = Auth::user()->children()->findOrFail($id);
        $child->delete();

        return $this->respondOk(null, 'Child deleted successfully');
    }
}
