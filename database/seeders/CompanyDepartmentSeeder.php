<?php

namespace Database\Seeders;

use App\Models\CompanyDepartment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Продажи',
                'description' => '',
                'is_published' => true,
            ],
            [
                'name' => 'Сервис',
                'description' => '',
                'is_published' => true,
            ],
            [
                'name' => 'Клиентский',
                'description' => '',
                'is_published' => true,
            ],
            [
                'name' => 'Администрация',
                'description' => '',
                'is_published' => true,
            ],
        ];

        foreach ($departments as $department) {
            CompanyDepartment::create($department);
        }

    }
}
