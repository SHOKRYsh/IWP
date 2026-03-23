<?php

namespace Modules\Task\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Task\Models\Habit;
use Modules\Task\Models\HabitCompletion;

class HabitController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('sanctum')->user();
        $query = Habit::query();

        if ($user->hasRole('Admin')) {
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        } else {
            $query->where('user_id', $user->id);
        }

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('frequency')) {
            $query->where('frequency', $request->frequency);
        }

        $habits = $query->latest()->paginate();

        return $this->respondOk($habits, 'Habits retrieved successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon_key' => 'nullable|string|max:255',
            'daily_goal' => 'required|integer|min:1',
            'frequency' => 'nullable|string|max:255',
            'daily_reminder_at' => 'nullable|date_format:H:i',
        ]);

        $validated['user_id'] = auth('sanctum')->id();
        $habit = Habit::create($validated);

        return $this->respondCreated($habit, 'Habit created successfully');
    }

    public function show($id)
    {
        $habit = Habit::where('user_id', auth('sanctum')->id())->findOrFail($id);
        return $this->respondOk($habit, 'Habit retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $habit = Habit::where('user_id', auth('sanctum')->id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'icon_key' => 'nullable|string|max:255',
            'daily_goal' => 'sometimes|required|integer|min:1',
            'frequency' => 'nullable|string|max:255',
            'daily_reminder_at' => 'nullable|date_format:H:i',
        ]);

        $habit->update($validated);

        return $this->respondOk($habit, 'Habit updated successfully');
    }

    public function destroy($id)
    {
        $habit = Habit::where('user_id', auth('sanctum')->id())->findOrFail($id);
        $habit->delete();

        return $this->respondOk(null, 'Habit deleted successfully');
    }

    public function complete($id)
    {
        $user = auth('sanctum')->user();
        $habit = Habit::where('user_id', $user->id)->findOrFail($id);
        $now = now();

        $alreadyCompletedToday = $habit->is_completed_today;

        $completion = HabitCompletion::create([
            'habit_id' => $habit->id,
            'user_id' => $user->id,
            'completed_at' => $now,
        ]);

        if (!$alreadyCompletedToday) {
            $lastCompletedAt = $habit->last_completed_at;
            if ($lastCompletedAt && $lastCompletedAt->isYesterday()) {
                $habit->streak_count++;
            } else {
                $habit->streak_count = 1;
            }
        }

        $habit->last_completed_at = $now;
        $habit->save();

        return $this->respondOk($habit, 'Habit completion logged successfully');
    }
}
