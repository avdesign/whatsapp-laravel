<?php
declare(strict_types=1);

namespace CodeShopping\Http\Controllers\Api;

use CodeShopping\Http\Resources\OrderResource;
use CodeShopping\Rules\OrderPaymentLinkChange;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Filters\OrderFilter;
use CodeShopping\Rules\OrderStatusChange;
use CodeShopping\Models\Order;
use Illuminate\Http\Request;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $filter = app(OrderFilter::class);
        $filterQuery = Order::with(['product', 'user'])->filtered($filter);
        $orders = $filterQuery->paginate();

        return OrderResource::collection($orders);
    }



    /**
     * Display the specified resource.
     *
     * @param  \CodeShopping\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        return new OrderResource($order);
    }

    /**
     * Status:
     * @param Request $request
     * @param Order $order
     */
    public function update(Request $request, Order $order)
    {

        /**
         * Status pode ser null e não pode alterar para STAUS_PENDING
         * Rules/OrderStatusChange
         */
        $this->validate($request, [
            'status' => [
                'nullable',
                'in:' .Order::STATUS_APPROVED.','. Order::STATUS_CANCELLED.','.Order::STATUS_SENT,
                new OrderStatusChange($order->status)
            ],
            'payment_link' => [
                'nullable',
                'url',
                new OrderPaymentLinkChange($order->status)
            ]
        ]);



        // php 7(??) se existe altera ou mantem o value
        $order->payment_link = $request->get('payment_link')??$order->payment_link;
        $order->status = $request->get('status')??$order->status;
        $order->obs = $request->get('obs')??$order->obs;
        $order->updateWithProduct();

        return new OrderResource($order);
    }

}
