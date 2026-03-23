<?php

namespace Modules\Task\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Task\Models\Task;
use Modules\Task\Models\Habit;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('sanctum')->user();
        $query = Task::with('lifeElement');

        if ($user->hasRole('Admin')) {
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        } else {
            $query->where('user_id', $user->id);
        }

        if ($request->has('due_date')) {
            $date = $request->due_date == 'today' ? now()->toDateString() : $request->due_date;
            $query->whereDate('due_date', $date);
        }

        if ($request->has('completed')) {
            if ($request->completed) {
                $query->whereNotNull('completed_at');
            } else {
                $query->whereNull('completed_at');
            }
        }

        if ($request->has('life_element_id')) {
            $query->where('life_element_id', $request->life_element_id);
        }

        $tasks = $query->latest()->paginate();
        return $this->respondOk($tasks, 'Tasks retrieved successfully');
    }

    public function getDailyProgress()
    {
        $userId = auth('sanctum')->id();
        $today = now()->toDateString();

        $totalTasksToday = Task::where('user_id', $userId)
            ->whereDate('due_date', $today)
            ->count();
        $completedTasksToday = Task::where('user_id', $userId)
            ->whereDate('due_date', $today)
            ->whereNotNull('completed_at')
            ->count();
        
        $taskPercentage = $totalTasksToday > 0 ? ($completedTasksToday / $totalTasksToday) * 100 : 0;
        
        $totalHabits = Habit::where('user_id', $userId)->count();
        $completedHabitsToday = Habit::where('user_id', $userId)
            ->whereNotNull('last_completed_at')
            ->get()
            ->filter(fn($habit) => $habit->last_completed_at->isToday())
            ->count();

        $habitPercentage = $totalHabits > 0 ? ($completedHabitsToday / $totalHabits) * 100 : 0;

        return $this->respondOk([
            'tasks' => [
                'total' => $totalTasksToday,
                'completed' => $completedTasksToday,
                'percentage' => round($taskPercentage, 2),
            ],
            'habits' => [
                'total' => $totalHabits,
                'completed' => $completedHabitsToday,
                'percentage' => round($habitPercentage, 2),
            ],
            'overall_percentage' => round(($taskPercentage + $habitPercentage) / 2, 2),
        ], 'Daily progress retrieved successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon_key' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|in:high,medium,low',
            'life_element_id' => 'required|exists:life_elements,id',
            'life_task_type_id' => 'nullable|exists:life_task_types,id',
            'due_date' => 'nullable|date',
            'reminder_at' => 'nullable|date',
            'extra_data' => 'nullable|array',
        ]);

        $validated['user_id'] = auth('sanctum')->id();
        $task = Task::create($validated);

        return $this->respondCreated($task, 'Task created successfully');
    }

    public function show($id)
    {
        $task = Task::with('lifeElement')->where('user_id', auth('sanctum')->id())->findOrFail($id);
        return $this->respondOk($task, 'Task retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $task = Task::where('user_id', auth('sanctum')->id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'icon_key' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|in:high,medium,low',
            'life_element_id' => 'nullable|exists:life_elements,id',
            'life_task_type_id' => 'nullable|exists:life_task_types,id',
            'due_date' => 'nullable|date',
            'reminder_at' => 'nullable|date',
            'extra_data' => 'nullable|array',
        ]);

        $task->update($validated);

        return $this->respondOk($task, 'Task updated successfully');
    }

    public function destroy($id)
    {
        $task = Task::where('user_id', auth('sanctum')->id())->findOrFail($id);
        $task->delete();

        return $this->respondOk(null, 'Task deleted successfully');
    }

    public function complete($id)
    {
        $task = Task::where('user_id', auth('sanctum')->id())->findOrFail($id);
        $task->update(['completed_at' => now()]);

        return $this->respondOk($task, 'Task marked as completed');
    }
}
