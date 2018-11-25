<?php

namespace CodeShopping\Http\Controllers\Api;

use CodeShopping\Http\Resources\ProductPhotoResource;
use CodeShopping\Http\Requests\ProductPhotoRequest;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Models\ProductPhoto;
use CodeShopping\Models\Product;

use Illuminate\Http\Request;
use CodeShopping\Http\Resources\ProductPhotoCollection;

class ProductPhotoController extends Controller
{
    public function index(Product $product)
    {

        return new ProductPhotoCollection($product->photos, $product);
    }

    public function store(ProductPhotoRequest $request, Product $product)
    {
        $photos =  ProductPhoto::createWithPhotosFiles($product->id, $request->photos);
        return response()->json(new ProductPhotoCollection($photos, $product), 201);
    }

    public function show(Product $product, ProductPhoto $photo)
    {
        $this->assertProductPhoto($product, $photo);
        return new ProductPhotoResource($photo);
    }

    /**
     * @param ProductPhotoRequest $request
     * @param Product $product
     * @param ProductPhoto $photo
     * @return ProductPhotoResource
     */
    public function update(ProductPhotoRequest $request, Product $product, ProductPhoto $photo)
    {
        $this->assertProductPhoto($product, $photo);
        $photo = $photo->updateWithPhoto($request->photo);
        return new ProductPhotoResource($photo);
    }

    /**
     * @param Product $product
     * @param ProductPhoto $photo
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Product $product, ProductPhoto $photo)
    {
        $this->assertProductPhoto($product, $photo);
        $photo->deleteWithPhoto();
        return response()->json([], 208);
    }


    /**
     * @param Product $product
     * @param ProductPhoto $photo
     * @return void
     */
    private function assertProductPhoto(Product $product, ProductPhoto $photo): void
    {
        if ($photo->product_id != $product->id) {
            abort(404);
        }
    }
}
