<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'name',
    ];

    public function productionItems(): HasMany
    {
        return $this->hasMany(ProductionItem::class);
    }
}
