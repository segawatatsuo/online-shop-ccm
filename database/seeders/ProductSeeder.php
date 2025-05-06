<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // まずカテゴリを5つ作成
        Category::factory(5)->create();

        // それぞれのカテゴリに対して商品をランダムに生成
        Product::factory(50)->create();
    }
}
