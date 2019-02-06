<?php

namespace CodeShopping\Http\Filters;

use Mnabialek\LaravelEloquentFilter\Filters\SimpleQueryFilter;

class OrderFilter extends SimpleQueryFilter
{
    protected $simpleFilters = ['search'];
    protected $simpleSorts = ['id', 'total', 'status', 'created_at', 'user', 'product'];

    protected function applySearch($value){
        $this->query
            ->where('users.name', 'LIKE', "%$value%")
            ->orWhere('products.name', 'LIKE', "%$value%");
    }

    protected function applySortUser($order){
        $this->query->orderBy('users.name', $order);
    }

    protected function applySortProduct($order){
        $this->query->orderBy('products.name', $order);
    }

    public function apply($query)
    {
        $query = $query
            ->select('orders.*')
            ->join('products', 'products.id', '=', 'orders.product_id')
            ->join('users', 'users.id', '=', 'orders.user_id');
        return parent::apply($query);
    }


}