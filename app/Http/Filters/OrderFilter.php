<?php

namespace CodeShopping\Http\Filters;

use Mnabialek\LaravelEloquentFilter\Filters\SimpleQueryFilter;

class OrderFilter extends SimpleQueryFilter
{
    protected $simpleFilters = ['search'];
    protected $simpleSorts = ['id', 'total', 'status', 'created_at'];

    protected function applySearch($value){
        $this->query->where('total', 'LIKE', "%$value%");
    }

    public function hasFilterParameter(){
        $contains = $this->parser->getFilters()->contains(function ($filter){
            return $filter->getField() === 'search' && !empty($filter->getValue());
        });
        return $contains;
    }

    protected function applyInterval()
    {
        //Bsca por intervalo de datas
    }

}