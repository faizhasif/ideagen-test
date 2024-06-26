<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(2),
            'category' => fake()->word(),
            'price' => fake()->randomFloat(2, 1, 50),
            'description' => fake()->paragraph(),
        ];
    }

    public function definedCategories(array $categories = null): static
    {
        if (is_null($categories)) {
            $categories = [
                'Foods',
                'Electronics',
                'Clothing',
            ];
        }

        return $this->state(fn (array $attributes) => [
            'category' => array_rand(array_flip($categories)),
        ]);
    }
}
