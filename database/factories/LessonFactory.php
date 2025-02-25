<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Lesson;
use Illuminate\Support\Str;

class LessonFactory extends Factory
{
    /**
     * The name of the corresponding model.
     *
     * @var string
     */
    protected $model = Lesson::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence, // Название урока
            'announcement' => $this->faker->paragraph, // Описание урока
            'lesson_content' => $this->faker->randomHtml(2, 5), // HTML содержимое урока
//            'position' => $this->faker->numberBetween(1, 10), // Продолжительность урока (минуты)
            'position' => null,
            'course_id' => null, // Определяется в LessonSeeder
        ];
    }
}
