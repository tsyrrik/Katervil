<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ProductionItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use function in_array;

class ProductionController extends Controller
{
    public function index(Request $request)
    {
        $sortable = ['name', 'quantity', 'thickness', 'unit_cost', 'completed_at', 'employee_name'];
        $sort = $request->string('sort')->lower()->value();
        $direction = $request->string('direction')->lower()->value() === 'desc' ? 'desc' : 'asc';

        if (!in_array($sort, $sortable, true)) {
            $sort = 'name';
        }

        $query = ProductionItem::with('employee');

        if ($sort === 'employee_name') {
            $query->leftJoin('employees as sort_employees', 'production_items.employee_id', '=', 'sort_employees.id')
                ->select('production_items.*')
                ->orderBy('sort_employees.name', $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        $items = $query->get();

        $employees = Employee::query()->orderBy('name')->get();

        return view('production.index', [
            'items' => $items,
            'employees' => $employees,
            'currentSort' => $sort,
            'currentDirection' => $direction,
        ]);
    }

    public function complete(Request $request, ProductionItem $productionItem)
    {
        $validated = $request->validate([
            'employee_id' => ['required', Rule::exists('employees', 'id')],
        ]);

        $productionItem->update([
            'employee_id' => $validated['employee_id'],
            'completed_at' => now(),
            'actual_quantity' => null,
            'actual_minutes' => null,
        ]);

        return redirect()
            ->route('production.index')
            ->with('status', "Операция для «{$productionItem->name}» зафиксирована.");
    }
}
