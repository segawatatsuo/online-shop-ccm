<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Category;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'price' => $this->faker->numberBetween(500, 5000),
            'description' => $this->faker->sentence(),
            'category_id' => Category::factory(), // ← ここでカテゴリも生成されます
            'image' => null, // またはダミーパスでも可
        ];
    }
}
