<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseAndLessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            '1' => [
                'name' => 'Основы ремонта двигателя',
                'announcement' => 'Научитесь основам ремонта двигателя и станьте увереннее в обслуживании своего автомобиля!',
                'description' => 'Курс для начинающих по ремонту двигателя',
                'duration' => '60',
                'is_published' => true,
                'course_type_id' => 3,
                'course_level_id' => 1,
                'course_category_id' => 1,
                'lessons' => [
                    [
                        'name' => 'Устройство двигателя',
                        'announcement' => 'Обзор основных компонентов двигателя',
                        'lesson_content' => 'Распознавание деталей двигателя',
                        'position' => 1,
                        'is_published' => true,
                    ],
                    [
                        'name' => 'Система охлаждения',
                        'announcement' => 'Принцип работы и диагностика неисправностей',
                        'lesson_content' => 'Проверка уровня охлаждающей жидкости',
                        'position' => 2,
                        'is_published' => true,
                    ],
                    [
                        'name' => 'Система зажигания',
                        'announcement' => 'Основные компоненты и их функции',
                        'lesson_content' => 'Замена свечей зажигания',
                        'position' => 3,
                        'is_published' => true,
                    ],
                    [
                        'name' => 'Система смазки',
                        'announcement' => 'Принцип работы и обслуживание',
                        'lesson_content' => 'Замена масла в двигателе',
                        'position' => 4,
                        'is_published' => true,
                    ],
                    [
                        'name' => 'Диагностика двигателя',
                        'announcement' => 'Основы диагностики неисправностей двигателя',
                        'lesson_content' => 'Использование диагностического оборудования',
                        'position' => 5,
                        'is_published' => true,
                    ]
                ]
            ],
            '2' => [
                'name' => 'Электрические системы автомобиля',
                'announcement' => 'Разберитесь в электрике автомобиля и научитесь диагностировать и устранять неисправности!',
                'description' => 'Курс по электрическим системам автомобиля',
                'duration' => '90',
                'is_published' => true,
                'course_type_id' => 1,
                'course_level_id' => 2,
                'course_category_id' => 2,
                'lessons' => [
                    [
                        'name' => 'Основы электрики автомобиля',
                        'announcement' => 'Понимание электрических схем',
                        'lesson_content' => 'Использование мультиметра',
                        'position' => 1,
                        'is_published' => true,
                    ],
                    [
                        'name' => 'Аккумулятор и система зарядки',
                        'announcement' => 'Техническое обслуживание и диагностика',
                        'lesson_content' => 'Проверка состояния аккумулятора',
                        'position' => 2,
                        'is_published' => true,
                    ],
                    [
                        'name' => 'Система освещения и сигнализации',
                        'announcement' => 'Устройство и ремонт фар и сигнализаций',
                        'lesson_content' => 'Замена ламп фар',
                        'position' => 3,
                        'is_published' => true,
                    ],
                    [
                        'name' => 'Система сигнализации и противоугонных систем',
                        'announcement' => 'Основные принципы работы и диагностика',
                        'lesson_content' => 'Проверка работоспособности сигнализации',
                        'position' => 4,
                        'is_published' => true,
                    ],
                    [
                        'name' => 'Ремонт электрических систем',
                        'announcement' => 'Практические навыки ремонта электрических систем',
                        'lesson_content' => 'Ремонт неисправной проводки',
                        'position' => 5,
                        'is_published' => true,
                    ]
                ]
            ],
            '3' => [
                'name' => 'Трансмиссия и тормозная система',
                'announcement' => 'Освойте навыки обслуживания трансмиссии и тормозной системы для безопасной и эффективной езды!',
                'description' => 'Курс по ремонту трансмиссии и тормозной системы',
                'duration' => '120',
                'is_published' => true,
                'course_type_id' => 2,
                'course_level_id' => 3,
                'course_category_id' => 3,
                'lessons' => [
                    [
                        'name' => 'Основы трансмиссии',
                        'announcement' => 'Типы коробок передач и их обслуживание',
                        'lesson_content' => 'Замена масла в коробке передач',
                        'position' => 1,
                        'is_published' => true,
                    ],
                    [
                        'name' => 'Тормозная система',
                        'announcement' => 'Принцип работы и диагностика неисправностей',
                        'lesson_content' => 'Проверка состояния тормозных колодок',
                        'position' => 2,
                        'is_published' => true,
                    ],
                    [
                        'name' => 'Ремонт тормозной системы',
                        'announcement' => 'Замена тормозных колодок и роторов',
                        'lesson_content' => 'Замена тормозных колодок',
                        'position' => 3,
                        'is_published' => true,
                    ],
                    [
                        'name' => 'Система рулевого управления',
                        'announcement' => 'Устройство и ремонт рулевого управления',
                        'lesson_content' => 'Проверка уровня жидкости в рулевом управлении',
                        'position' => 4,
                        'is_published' => true,
                    ],
                    [
                        'name' => 'Диагностика трансмиссии и тормозов',
                        'announcement' => 'Основы диагностики неисправностей',
                        'lesson_content' => 'Использование диагностического оборудования',
                        'position' => 5,
                        'is_published' => true,
                    ]
                ]
            ],
        ];

        // Здесь должен быть код для сохранения курсов в базу данных
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Lesson::truncate();
        Course::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($courses as $courseData) {
            $course = Course::create([
                'name' => $courseData['name'],
                'announcement' => $courseData['announcement'],
                'description' => $courseData['description'],
                'duration' => $courseData['duration'],
                'is_published' => $courseData['is_published'],
                'course_type_id' => $courseData['course_type_id'],
                'course_level_id' => $courseData['course_level_id'],
                'course_category_id' => $courseData['course_category_id'],
            ]);

            foreach ($courseData['lessons'] as $lessonData) {
                $lesson = Lesson::create([
                    'name' => $lessonData['name'],
                    'announcement' => $lessonData['announcement'],
                    'lesson_content' => $lessonData['lesson_content'],
                    'position' => $lessonData['position'],
                    'is_published' => $lessonData['is_published'],
                    'course_id' => $course->id,
                ]);
            }
        }
    }
}
