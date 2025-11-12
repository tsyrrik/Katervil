<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\ProductionItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $supervisor = User::updateOrCreate(
            ['email' => 'boss@example.com'],
            [
                'name' => 'Начальник смены',
                'password' => Hash::make('password'),
                'role' => User::ROLE_SUPERVISOR,
            ],
        );

        User::updateOrCreate(
            ['email' => 'worker@example.com'],
            [
                'name' => 'Сотрудник',
                'password' => Hash::make('password'),
                'role' => User::ROLE_WORKER,
            ],
        );

        $employees = collect([
            'Иванов',
            'Петров',
            'Сидоров',
        ])->mapWithKeys(static fn($name) => [
            $name => Employee::firstOrCreate(['name' => $name]),
        ]);

        $items = [
            [
                'name' => 'Профиль основной',
                'operation_code' => 'ULTRA-1.0-01.00.01 ПРОФИЛЬ',
                'quantity' => 10,
                'thickness' => 3.0,
                'unit_cost' => 30,
            ],
            [
                'name' => 'Ухо щит',
                'operation_code' => 'ULTRA-1.0-01.00.04 УХО',
                'quantity' => 120,
                'thickness' => 2.0,
                'unit_cost' => 20,
            ],
            [
                'name' => 'Щит',
                'operation_code' => 'ULTRA-1.0-01.00.07 ЩИТ',
                'quantity' => 20,
                'thickness' => 0.8,
                'unit_cost' => 12,
            ],
            [
                'name' => 'Боковина',
                'operation_code' => 'ULTRA-1.0-01.00.12 БОКОВИНА',
                'quantity' => 20,
                'thickness' => 1.5,
                'unit_cost' => 15,
            ],
        ];

        foreach ($items as $item) {
            ProductionItem::firstOrCreate(
                ['name' => $item['name']],
                $item,
            );
        }
    }
}
