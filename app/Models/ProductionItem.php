<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property string $operation_code
 * @property int $quantity
 * @property int|null $actual_quantity
 * @property int|null $actual_minutes
 * @property float $thickness
 * @property float $unit_cost
 * @property int|null $employee_id
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property-read Employee|null $employee
 */
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

        return (float) $quantity * (float) $this->unit_cost;
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null && $this->employee_id !== null;
    }
}
