<?php

use Illuminate\Database\Seeder;
use CodeShopping\Models\Product;
use CodeShopping\Models\Category;


class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Consultar as categorias
        /** @var \Illuminate\Database\Eloquent\Collection */
        $categories = Category::all();

        factory(Product::class, 30)
        ->create()
        ->each(function(Product $product) use($categories){
            $categoryId = $categories->random()->id;
            $product->categories()->attach($categoryId);
        });
    }
}
