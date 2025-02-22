<?php

namespace Database\Seeders;

use App\Models\CompanyPosition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            [
                'name' => 'Продавец',
                'description' => '',
                'is_published' => true,
            ],
            [
                'name' => 'Механик',
                'description' => '',
                'is_published' => true,
            ],
            [
                'name' => 'Мастер приемщик',
                'description' => '',
                'is_published' => true,
            ],
            [
                'name' => 'Директор магазина',
                'description' => '',
                'is_published' => true,
            ],
        ];

        foreach ($positions as $position) {
            CompanyPosition::create($position);
        }

    }
}
