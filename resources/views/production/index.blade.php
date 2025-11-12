@extends('layouts.app')

@section('title', 'Производство')

@php
    $today = now()->format('d.m.Y');
    $nextDirection = fn(string $column) => $currentSort === $column && $currentDirection === 'asc' ? 'desc' : 'asc';
    $sortIcon = function (string $column) use ($currentSort, $currentDirection) {
        if ($currentSort !== $column) {
            return '<span class="text-muted">↕</span>';
        }

        return $currentDirection === 'asc' ? '↑' : '↓';
    };
@endphp

@section('content')
    @if($errors->any())
        <div class="container pt-4">
            <div class="alert alert-error">{{ $errors->first() }}</div>
        </div>
    @endif

    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header border-bottom-0 pb-0">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('production.*') ? 'active' : '' }}" href="{{ route('production.index') }}">Производство</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('salary.index') }}">Зарплата</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h1 class="h4 mb-0">Производство</h1>
                        <small class="text-muted">Учёт выполнения операций резки</small>
                    </div>
                    <span class="badge text-bg-secondary">Позиций: {{ $items->count() }}</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start">
                                    <a class="text-decoration-none d-inline-flex align-items-center gap-1 justify-content-center" href="{{ route('production.index', ['sort' => 'name', 'direction' => $nextDirection('name')]) }}">
                                        Наименование {!! $sortIcon('name') !!}
                                    </a>
                                </th>
                                <th>
                                    <a class="text-decoration-none d-inline-flex align-items-center gap-1" href="{{ route('production.index', ['sort' => 'quantity', 'direction' => $nextDirection('quantity')]) }}">
                                        Кол-во {!! $sortIcon('quantity') !!}
                                    </a>
                                </th>
                                <th>
                                    <a class="text-decoration-none d-inline-flex align-items-center gap-1" href="{{ route('production.index', ['sort' => 'thickness', 'direction' => $nextDirection('thickness')]) }}">
                                        Толщина {!! $sortIcon('thickness') !!}
                                    </a>
                                </th>
                                <th>
                                    <a class="text-decoration-none d-inline-flex align-items-center gap-1" href="{{ route('production.index', ['sort' => 'unit_cost', 'direction' => $nextDirection('unit_cost')]) }}">
                                        Стоимость, руб/шт {!! $sortIcon('unit_cost') !!}
                                    </a>
                                </th>
                                <th>
                                    <a class="text-decoration-none d-inline-flex align-items-center gap-1" href="{{ route('production.index', ['sort' => 'employee_name', 'direction' => $nextDirection('employee_name')]) }}">
                                        Операция резки {!! $sortIcon('employee_name') !!}
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td class="text-start">
                                        <div class="fw-semibold">{{ $item->name }}</div>

                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ rtrim(rtrim(number_format($item->thickness, 2, ',', ''), '0'), ',') }}</td>
                                    <td>{{ number_format($item->unit_cost, 0, '', ' ') }}</td>
                                    <td>
                                        @if($item->isCompleted() && auth()->user()->isSupervisor())
                                            <button
                                                type="button"
                                                class="btn btn-link p-0 text-primary js-open-modal"
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->name }}"
                                                data-operation="{{ $item->operation_code }}"
                                                data-quantity="{{ $item->quantity }}"
                                                data-employee="{{ $item->employee_id }}"
                                            >{{ $item->employee->name }}</button>
                                        @elseif(!$item->isCompleted() && auth()->user()->isSupervisor())
                                            <button
                                                type="button"
                                                class="btn btn-link p-0 text-primary js-open-modal"
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->name }}"
                                                data-operation="{{ $item->operation_code }}"
                                                data-quantity="{{ $item->quantity }}"
                                                data-employee=""
                                            >Готово</button>
                                        @elseif($item->isCompleted())
                                            <span class="badge text-bg-success">{{ $item->employee->name }}</span>
                                        @else
                                            <span class="badge text-bg-secondary">Ожидает подтверждения</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cuttingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <div class="w-100 text-center">
                        <h5 class="modal-title mb-1">Операция резки</h5>
                        <p class="text-muted small mb-0" id="operation-code">—</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="cutting-form" method="POST">
                        @csrf
                        <div class="row g-3 mb-1">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Дата</label>
                                <input type="text" class="form-control" value="{{ $today }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Выполнил</label>
                                <select class="form-select" name="employee_id" id="employee-select" required>
                                    <option value="">Выберите</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Количество</label>
                                <input type="text" class="form-control" id="operation-quantity" readonly>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" form="cutting-form" class="btn btn-primary px-4">Готово</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const cuttingModalElement = document.getElementById('cuttingModal');
    const cuttingModal = new bootstrap.Modal(cuttingModalElement);
    const cuttingForm = document.getElementById('cutting-form');
    const employeeSelect = document.getElementById('employee-select');
    const operationCode = document.getElementById('operation-code');
    const operationQuantity = document.getElementById('operation-quantity');

    document.querySelectorAll('.js-open-modal').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.id;
            cuttingForm.action = `{{ url('/production') }}/${id}/complete`;
            operationCode.textContent = button.dataset.operation || '—';
            operationQuantity.value = button.dataset.quantity;
            employeeSelect.value = button.dataset.employee || '';
            cuttingModal.show();
        });
    });

    cuttingModalElement.addEventListener('hidden.bs.modal', () => {
        cuttingForm.action = '';
        employeeSelect.value = '';
    });
</script>
@endpush
