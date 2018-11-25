<?php

use Illuminate\Database\Seeder;

class ProductInputsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = \CodeShopping\Models\Product::all();
        factory(\CodeShopping\Models\ProductInput::class,200)
            ->make()
            ->each(function($input) use($products){
                $product = $products->random();
                $input->product_id = $product->id;
                $input->save();
            });

    }
}
