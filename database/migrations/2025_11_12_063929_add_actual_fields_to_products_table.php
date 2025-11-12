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
        Schema::table('production_items', static function (Blueprint $table): void {
            $table->unsignedInteger('actual_quantity')->nullable()->after('quantity');
            $table->unsignedInteger('actual_minutes')->nullable()->after('actual_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_items', static function (Blueprint $table): void {
            $table->dropColumn(['actual_quantity', 'actual_minutes']);
        });
    }
};
