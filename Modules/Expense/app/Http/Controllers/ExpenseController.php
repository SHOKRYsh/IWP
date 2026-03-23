<?php

namespace Modules\Expense\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Expense\Models\Expense;
use Modules\Expense\Models\MonthlyBudget;

class ExpenseController extends Controller
{
    public function getBudget(Request $request)
    {
        $user = auth('sanctum')->user();
        $budget = MonthlyBudget::query();

        if ($user->hasRole('Admin')) {
            if ($request->has('user_id')) {
                $budget->where('user_id', $request->user_id);
            }
        } else {
            $budget->where('user_id', $user->id);
        }

        if ($request->has('month')) {
            $budget->where('month', $request->month);
        }

        if ($request->has('year')) {
            $budget->where('year', $request->year);
        }

        $budget = $budget->latest()->paginate();
        return $this->respondOk($budget, 'Budget retrieved successfully');
    }

    public function setBudget(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
        ]);

        $budget = MonthlyBudget::updateOrCreate(
            [
                'user_id' => auth('sanctum')->id(),
                'month' => $validated['month'],
                'year' => $validated['year'],
            ],
            ['amount' => $validated['amount']]
        );

        return $this->respondOk($budget, 'Budget set successfully');
    }
    public function index(Request $request)
    {
        $user = auth('sanctum')->user();
        $query = Expense::with('lifeTaskType')->where('user_id', $user->id);

        if ($request->has('life_task_type_id')) {
            $query->where('life_task_type_id', $request->life_task_type_id);
        }

        if ($request->has('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        if ($request->has('payment')) {
            $query->where('payment', $request->payment);
        }

        $expenses = $query->latest()->paginate();
        return $this->respondOk($expenses, 'Expenses retrieved successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'life_task_type_id' => 'required|exists:life_task_types,id',
            'date' => 'required|date',
            'payment' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $validated['user_id'] = auth('sanctum')->id();
        $expense = Expense::create($validated);

        return $this->respondCreated($expense, 'Expense recorded successfully');
    }

    public function getAnalysis(Request $request)
    {
        $userId = auth('sanctum')->id();
        
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $budget = MonthlyBudget::where('user_id', $userId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        $expenses = Expense::with('lifeTaskType')
            ->where('user_id', $userId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $totalExpense = $expenses->sum('amount');
        $budgetAmount = $budget ? $budget->amount : 0;

        $analysis = $expenses->groupBy('life_task_type_id')->map(function ($group) use ($totalExpense, $budgetAmount) {
            $categoryTotal = $group->sum('amount');
            $lifeTaskType = $group->first()->lifeTaskType;

            return [
                'life_task_type_id' => $lifeTaskType ? $lifeTaskType->id : null,
                'category_name' => $lifeTaskType ? $lifeTaskType->name : 'Uncategorized',
                'amount' => $categoryTotal,
                'percentage_of_total' => $totalExpense > 0 ? round(($categoryTotal / $totalExpense) * 100, 2) : 0,
                'percentage_of_budget' => $budgetAmount > 0 ? round(($categoryTotal / $budgetAmount) * 100, 2) : 0,
            ];
        })->values();

        return $this->respondOk([
            'month' => $month,
            'year' => $year,
            'budget' => $budgetAmount,
            'total_expense' => $totalExpense,
            'remaining_budget' => round($budgetAmount - $totalExpense, 2),
            'categories' => $analysis,
        ], 'Expense analysis retrieved successfully');
    }
}
