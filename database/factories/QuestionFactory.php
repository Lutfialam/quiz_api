<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'quiz_id' => $this->faker->numberBetween(1, 10),
            'question' => $this->faker->text,
            'first_choice' => $this->faker->text,
            'second_choice' => $this->faker->text,
            'third_choice' => $this->faker->text,
            'fourth_choice' => $this->faker->text,
            'answer' => $this->faker->randomElement(['A', 'B', 'C', 'D']),
        ];
    }
}
