<?php

namespace CodeShopping\Http\Controllers\Api\Open;

use CodeShopping\Http\Resources\Open\OrderResource as OpenOrderResource;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Exists;

class OrderController extends Controller
{
    public function index()
    {
        $userId = \Auth::guard('api')->user()->id;
        $orders = Order::where('user_id', $userId)->paginate();

        return OpenOrderResource::collection($orders);
    }

    public function show(Order $order)
    {
        $this->assertOrder($order);

        return new OpenOrderResource($order);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'product_id' => [
                'required',
                (new Exists('products', 'id'))->where(function ($query) {
                    return $query
                        ->where('stock', '>', 0)
                        ->where('active', true);
                })
            ],
            'amount' => 'required|integer|min:1'
        ]);

        $order = Order::createWithProduct([
            'user_id' => \Auth::guard('api')->user()->id,
            'product_id' => $request->product_id,
            'amount' => $request->amount
        ]);

        $order->refresh();

        return new OpenOrderResource($order);
    }


    public function update(Order $order)
    {
        $this->assertOrder($order);

        if ($order->status != Order::STATUS_PENDING) {
            abort('403', 'Alteração do status do pedido não autorizado.');
        }

        $order->status = Order::STATUS_CANCELLED;
        $order->save();

        return new OpenOrderResource($order);
    }

    private function assertOrder(Order $order)
    {
        if ($order->user_id !== \Auth::guard('api')->user()->id)
        {
            abort('404', 'Você não esta autorizado para fazer este pedido');
        }
    }
}
