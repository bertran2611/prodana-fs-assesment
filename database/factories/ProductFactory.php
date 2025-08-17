<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
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
            'name'        => $this->faker->words(2, true), 
            'price'       => $this->faker->numberBetween(10000, 500000),
            'stock'       => $this->faker->numberBetween(0, 100),
            'category_id' => Category::inRandomOrder()->first()->id,
            'image_path'  => $this->faker->imageUrl(640, 480, 'products', true),
            'description' => $this->faker->sentence(8),
        ];
    }
}
