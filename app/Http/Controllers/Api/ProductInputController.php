<?php

namespace CodeShopping\Http\Controllers\Api;

use CodeShopping\Http\Filters\ProductInputFilter;
use CodeShopping\Http\Resources\ProductInputResource;
use CodeShopping\Http\Requests\ProductInputRequest;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Models\ProductInput;


class ProductInputController extends Controller
{
    public function index()
    {
        $filter = app(ProductInputFilter::class);
        $filterQuery = ProductInput::with('product')->filtered($filter);
        $inputs = $filterQuery->paginate();
        return ProductInputResource::collection($inputs);
    }

    public function store(ProductInputRequest $request)
    {
        $input = ProductInput::create($request->all());        

        return new ProductInputResource($input);

    }

    public function show(ProductInput $input)
    {
        return new ProductInputResource($input);
    }

}
