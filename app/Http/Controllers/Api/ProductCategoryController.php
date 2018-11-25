<?php

namespace CodeShopping\Http\Controllers\Api;

use CodeShopping\Http\Requests\ProductCategoryRequest;
use CodeShopping\Http\Resources\ProductCategoryResource;

use CodeShopping\Http\Controllers\Controller;

use CodeShopping\Models\Category;
use CodeShopping\Models\Product;



class ProductCategoryController extends Controller
{

    public function index(Product $product)
    {
        return new ProductCategoryResource($product);
    }

    public function store(ProductCategoryRequest $request, Product $product)
    {
        //return $product->categories()->sync($request->categories);
        $changed = $product->categories()->sync($request->categories);
        
        $categoriesAttachedId = $changed['attached'];

        $categories = Category::whereIn('id', $categoriesAttachedId)->get();

        //return $categories;
        return $categories->count() ? response()->json(new ProductCategoryResource($product), 201) : $categories;

    
    }

    public function destroy(Product $product, Category $category)
    {
        $product->categories()->detach($category->id);
        return response()->json([], 204);
    }

    
}
