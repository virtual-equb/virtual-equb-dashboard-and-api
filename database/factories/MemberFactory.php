<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'full_name' => $this->faker->full_name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->unique()->phone,
            'address' => $this->faker->address,
        ];
    }
}
