<?php

use Illuminate\Database\Seeder;

class OrdersTableSeeder extends Seeder
    {

    public function run()
    {
        $products = \CodeShopping\Models\Product::all();
        foreach (range(1, 20) as $v) {
            \CodeShopping\Models\Order::createWithProduct([
                'user_id' => 1,
                'product_id' => $products->random()->id,
                'amount' => rand(1, 2)
            ]);
        }
    }
}
