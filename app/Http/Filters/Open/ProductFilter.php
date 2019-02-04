<?php

namespace CodeShopping\Http\Filters\Open;

use Mnabialek\LaravelEloquentFilter\Filters\SimpleQueryFilter;

class ProductFilter extends SimpleQueryFilter
{
    protected $simpleFilters = ['search', 'categories'];
    protected $simpleSorts = ['price', 'created_at'];

    protected function applySearch($value){
        if (empty($value)) {
            return;
        }
        $this->query
            ->where('name', 'LIKE', "%$value%")
            ->orWhere('description', 'LIKE', '%$value%');
    }

    protected function applyCategories($value)
    {
        if (!is_array($value) || count($value) === 0) {
            return;
        }

        //dd($value);

        $this->query->whereHas('categories', function($query) use($value){
            $query->whereIn('id', $value)// WHERE ID IN (1,4,10,30)
            ->where('active', true);
        });


    }



}