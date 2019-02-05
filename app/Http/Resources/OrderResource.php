<?php

namespace CodeShopping\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'total' => (float)$this->total,
            'status' => (int)$this->status,
            'payment_link' => $this->payment_link,
            'product' => new ProductResource($this->product),
            'amount' => (int)$this->amount,
            'price' => (float)$this->price,
            'user' => new UserResource($this->user),
            'obs' => $this->obs,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
