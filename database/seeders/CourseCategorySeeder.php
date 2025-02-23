<?php

namespace Database\Seeders;

use App\Models\CourseCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courseCategories = [
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
                'name' => 'Персонал',
                'description' => '',
                'is_published' => true,
            ],
        ];

        foreach ($courseCategories as $courseCategory) {
            CourseCategory::create($courseCategory);
        }

    }
}
