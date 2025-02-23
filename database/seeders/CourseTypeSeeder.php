<?php

namespace Database\Seeders;

use App\Models\CourseType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courseTypes = [
            [
                'name' => 'Обязательный',
                'description' => '',
                'is_published' => true,
            ],
            [
                'name' => 'Дополнительный',
                'description' => '',
                'is_published' => true,
            ],
            [
                'name' => 'Вводный',
                'description' => '',
                'is_published' => true,
            ],
        ];

        foreach ($courseTypes as $courseType) {
            CourseType::create($courseType);
        }

    }
}
