<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Employee;

class SalaryController extends Controller
{
    public function index()
    {
        $employees = Employee::with('productionItems')
            ->orderBy('name')
            ->get();

        return view('salary.index', [
            'employees' => $employees,
        ]);
    }
}
