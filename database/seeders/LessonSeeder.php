<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем все курсы
        $courses = Course::all();

        // Для каждого курса создаем 5 уроков
        foreach ($courses as $course) {
            // Для каждого курса инициализируем позицию
            $position = 1;

            Lesson::factory()
                ->count(5)
                ->create([
                    'course_id' => $course->id, // Указываем связи с курсом
                    'position' => function () use (&$position) {
                        return $position++; // Увеличиваем позицию для каждого урока
                    },
                ]);
        }

    }
}
