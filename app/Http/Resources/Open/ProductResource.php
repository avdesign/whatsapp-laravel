<?php

namespace CodeShopping\Http\Resources\Open;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            //'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'photo_url' => env('BASE_PRODUCTS_URL') . $this->photo,
            //'stock' => (int) $this->stock,
            //'active' => (bool) $this->active,
            'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,

        ];
    }
}
