<?php

namespace App\Http\Controllers\Automation;

use App\Http\Controllers\Controller;
use App\Models\AccountingExpense;
use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountingExpenseController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->canManageAccounting()) {
            abort(403);
        }

        $expenses = AccountingExpense::with('creator')
            ->latest()
            ->paginate(15);

        return view('accounting.expenses.index', compact('expenses'));
    }

    public function create()
    {
        if (!Auth::user()->canManageAccounting()) {
            abort(403);
        }

        return view('accounting.expenses.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->canManageAccounting()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'expense_date' => 'required|string',
        ]);

        $validated['expense_date'] = JalaliDate::parse($validated['expense_date'])?->format('Y-m-d')
            ?? $validated['expense_date'];
        $validated['created_by'] = Auth::id();

        AccountingExpense::create($validated);

        return redirect()->route('automation.accounting.expenses.index')
            ->with('success', 'هزینه با موفقیت ثبت شد.');
    }

    public function edit(AccountingExpense $expense)
    {
        if (!Auth::user()->canManageAccounting()) {
            abort(403);
        }

        return view('accounting.expenses.edit', compact('expense'));
    }

    public function update(Request $request, AccountingExpense $expense)
    {
        if (!Auth::user()->canManageAccounting()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'expense_date' => 'required|string',
        ]);

        $validated['expense_date'] = JalaliDate::parse($validated['expense_date'])?->format('Y-m-d')
            ?? $validated['expense_date'];

        $expense->update($validated);

        return redirect()->route('automation.accounting.expenses.index')
            ->with('success', 'هزینه با موفقیت بروزرسانی شد.');
    }

    public function destroy(AccountingExpense $expense)
    {
        if (!Auth::user()->canManageAccounting()) {
            abort(403);
        }

        $expense->delete();

        return redirect()->route('automation.accounting.expenses.index')
            ->with('success', 'هزینه با موفقیت حذف شد.');
    }
}
