<?php

use Illuminate\Database\Seeder;

class OrdersTableSeeder extends Seeder
    {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = \CodeShopping\Models\Product::all();
        foreach (range(1, 20) as $v) {
            \CodeShopping\Models\Order::createWithProduct([
                'user_id' => 2,
                'product_id' => $products->random()->id,
                'amount' => rand(1, 2)
            ]);
        }
    }
}
