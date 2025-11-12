<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('production_items', static function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('operation_code')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->decimal('thickness', 5, 2);
            $table->decimal('unit_cost', 10, 2);
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_items');
    }
};
