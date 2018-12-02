<?php

namespace CodeShopping\Http\Filters;

use CodeShopping\Models\User;
use Mnabialek\LaravelEloquentFilter\Filters\SimpleQueryFilter;

class UserFilter extends SimpleQueryFilter
{
    /**
     * Define os nomes dos parâmetros
     * @var array
     */
    protected $simpleFilters = ['search', 'role'];
    /**
     * Define os campos de busca
     * @var array
     */
    protected $simpleSorts = ['id', 'name', 'email'];

    protected function applySearch($value){
        $this->query->where('name', 'LIKE', "%$value%")
                    ->orWhere('email', 'LIKE', "%$value%");
    }

    /**
     * Opções de busca (Vendedores ou Clientes)
     * @param $value
     */
    protected function applyRole($value)
    {
        $role = $value == 'customer' ? User::ROLE_CUSTOMER : User::ROLE_SELLER;
        $this->query->where('role', $role);
    }

    /**
     *
     * @return bool
     */
    public function hasFilterParameter()
    {
        $contains = $this->parser->getFilters()->contains(function ($filter){
            return $filter->getField() === 'search' && !empty($filter->getValue());
        });
        return $contains;
    }


}