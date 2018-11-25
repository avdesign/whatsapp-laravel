<?php

namespace CodeShopping\Http\Filters;

use Mnabialek\LaravelEloquentFilter\Filters\SimpleQueryFilter;

class UserFilter extends SimpleQueryFilter
{
    protected $simpleFilters = ['search'];
    protected $simpleSorts = ['id', 'name', 'email', 'created_at'];

    protected function applySearch($value){
        $this->query->where('name', 'LIKE', "%$value%")
                    ->orWhere('email', 'LIKE', "%$value%")
                    ->orWhere('created_at', 'LIKE', "%$value%");
    }

    protected function applyInterval()
    {
        //Bsca por intervalo de datas
    }


}