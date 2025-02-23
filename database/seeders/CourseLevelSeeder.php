<?php

namespace Database\Seeders;

use App\Models\CourseLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courseLevels = [
            [
                'name' => 'Начальный',
                'description' => '',
                'is_published' => true,
            ],
            [
                'name' => 'Средний',
                'description' => '',
                'is_published' => true,
            ],
            [
                'name' => 'Продвинутый',
                'description' => '',
                'is_published' => true,
            ],
        ];

        foreach ($courseLevels as $courseLevel) {
            CourseLevel::create($courseLevel);
        }

    }
}
