<?php

namespace CodeShopping\Models;

use Illuminate\Database\Eloquent\Model;
use Mnabialek\LaravelEloquentFilter\Traits\Filterable;


class Order extends Model
{
    use Filterable;

    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_CANCELLED = 3;
    const STATUS_SENT = 4;

    protected $fillable = ['amount', 'price', 'total', 'product_id', 'user_id'];

    /**
     * Criar Order
     * @param array $data
     */
    public static function createWithProduct(array $data)
    {
        $product = Product::find($data['product_id']);
        $data['price'] = $product->price;
        $data['total'] = $data['price'] * $data['amount'];
        self::create($data);
    }

    /**
     * Observers/OrderObserver/handleIfSent($order) lookForUpdate()
     * informa ao bd que o produto esta bloqueado enquanto não termna a transação
     * @throws \Exception
     */
    public function updateWithProduct()
    {
        try {
            \DB::beginTransaction();
            $this->save();
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    /**
     * Retorna o usuário mesmo excluido.
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Retorna o produto mesmo excluido.
     * @return mixed
     */
    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
