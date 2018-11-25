<?php

namespace CodeShopping\Models;

use Illuminate\Database\Eloquent\Model;
use Mnabialek\LaravelEloquentFilter\Traits\Filterable;


class ProductInput extends Model
{
    use Filterable;

    protected $fillable = ['amount', 'product_id'];

    public function product(){
        return $this->belongsTo(Product::class)->withTrashed();
    }

}
