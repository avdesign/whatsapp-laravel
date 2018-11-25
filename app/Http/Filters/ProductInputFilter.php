<?php

namespace CodeShopping\Http\Filters;

use Mnabialek\LaravelEloquentFilter\Contracts\Sort;
use Mnabialek\LaravelEloquentFilter\Filters\SimpleQueryFilter;


class ProductInputFilter extends SimpleQueryFilter
{
    protected $simpleFilters = ['search'];

    protected $simpleSorts = ['id', 'product_name', 'created_at'];

    protected function applySearch($value)
    {
        $this->query->where('name', 'LIKE', "%$value%");
    }

    /**
     * Ordenar pelo nome do produto
     * @param $order
     */
    protected function applySortProductName($order)
    {
        $this->query->orderBy('name', $order);
    }

    /**
     * Quando os campos das tbls forem com mesmo nome
     * Ordenar pela data do created_at tbl product_inputs
     * @param $order
     */
    protected function applySortCreatedAt($order)
    {
        $this->query->orderBy('product_inputs.created_at', $order);
    }

    /**
     * Ordenar pela quantidae
     * @param $order
     */
    protected function applySortAmount($order)
    {
        $this->query->orderBy('amount', $order);
    }

    /**
     * @param Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply($query)
    {
        $query = $query
            ->select('product_inputs.*')
            ->join('products', 'products.id', '=', 'product_inputs.product_id');
        return parent::apply($query);
    }

    //SELECT * FROM products_inputs JOIN products ON products.id = products_inputs.products_id
//Retorna id -> products_inputs = id -> product


}