<?php

namespace CodeShopping\Http\Controllers\Api;

use CodeShopping\Http\Resources\ProductOutputResource;
use CodeShopping\Http\Requests\ProductOutputRequest;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Models\ProductOutput;

class ProductOutputController extends Controller
{
    public function index()
    {
        $outputs = ProductOutput::with('product')->paginate();
        return ProductOutputResource::collection($outputs);
    }

    public function store(ProductOutputRequest $request)
    {
        $outputs = ProductOutput::create($request->all());        

        return new ProductOutputResource($outputs);

    }

    public function show(ProductOutput $output)
    {
        return new ProductOutputResource($output);
    }
}
