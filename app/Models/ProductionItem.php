<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionItem extends Model
{
    protected $fillable = [
        'name',
        'operation_code',
        'quantity',
        'actual_quantity',
        'actual_minutes',
        'thickness',
        'unit_cost',
        'employee_id',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'thickness' => 'float',
        'unit_cost' => 'float',
        'quantity' => 'integer',
        'actual_quantity' => 'integer',
        'actual_minutes' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getRewardAttribute(): float
    {
        $quantity = $this->actual_quantity ?? $this->quantity;

        return $quantity * $this->unit_cost;
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null && $this->employee_id !== null;
    }
}
