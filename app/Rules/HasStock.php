<?php

namespace CodeShopping\Rules;

use Illuminate\Contracts\Validation\Rule;
use CodeShopping\Models\Product;

class HasStock implements Rule
{
    /**
     * @var Product
     */
    private $product;


    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Product $product)
    {
        $this->products = $product;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->products->stock - $value >= 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "O Produto {$this->products->name} não pode ser menor que 0";
    }
}
