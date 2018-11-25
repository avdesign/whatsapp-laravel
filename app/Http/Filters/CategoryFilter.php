<?php

namespace CodeShopping\Http\Filters;

use Mnabialek\LaravelEloquentFilter\Filters\SimpleQueryFilter;

class CategoryFilter extends SimpleQueryFilter
{
    protected  $simpleFilters = ['search'];
    protected $simpleSorts = ['id', 'name', 'created_at'];

    protected function applySearch($value){
        $this->query->where('name', 'LIKE', "%$value%")
                    ->orWhere('slug', 'LIKE', "%$value%")
                    ->orWhere('created_at', 'LIKE', "%$value%");;
    }

    protected function applyInterval()
    {
        //Bsca por intervalo de datas
    }


}