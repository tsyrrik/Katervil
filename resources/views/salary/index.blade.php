@extends('layouts.app')

@section('title', 'Зарплата')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header border-bottom-0 pb-0">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('production.index') }}">Производство</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('salary.index') }}">Зарплата</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h1 class="h4 mb-0">Зарплата</h1>
                        <small class="text-muted">Вознаграждение за выполненные операции</small>
                    </div>
                    <span class="badge text-bg-secondary">Сотрудников: {{ $employees->count() }}</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Сотрудник</th>
                                <th>Выполнено операций</th>
                                <th>Начислено, руб</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @foreach($employees as $employee)
                                @php
                                    $completedItems = $employee->productionItems->filter->isCompleted();
                                    $completed = $completedItems->count();
                                    $reward = $completedItems->sum(fn($item) => $item->reward);
                                    $total += $reward;
                                @endphp
                                <tr>
                                    <td>{{ $employee->name }}</td>
                                    <td>{{ $completed }}</td>
                                    <td>{{ number_format($reward, 0, '', ' ') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-end">Итого:</th>
                                <th>{{ number_format($total, 0, '', ' ') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
