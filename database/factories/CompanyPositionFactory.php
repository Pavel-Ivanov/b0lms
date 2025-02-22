<?php

namespace Database\Factories;

use App\Models\CompanyPosition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanyPosition>
 */

class CompanyPositionFactory extends Factory
{
    protected $model = CompanyPosition::class;
    public function definition(): array
    {
        return [
            'name' => $this->faker->jobTitle,
            'description' => $this->faker->sentence,
            'is_published' => $this->faker->boolean,
        ];
    }
}
