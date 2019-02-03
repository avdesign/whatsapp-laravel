<?php

namespace CodeShopping\Http\Controllers\Api\Open;

use CodeShopping\Http\Resources\CategoryResource;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('active', true)
            ->orderBy('name')
            ->get();

        return CategoryResource::collection($categories);
    }
}
