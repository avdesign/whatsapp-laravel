<?php
declare(strict_types=1);

use Illuminate\Database\Seeder;

use CodeShopping\Models\Product;
use CodeShopping\Models\ProductPhoto;

use Illuminate\Support\Collection;


class ProductPhotosTableSeeder extends Seeder
{
    /**
     * @var Collection
     */
    private $allFakePhotos;
    private $fakePhotosPath = 'app/faker/product_photos';
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->allFakePhotos = $this->getFakePhotos();
        $products = Product::all();
        $this->deteleAllPhotosProductsPath();
        $self = $this;
        $products->each(function($products) use($self){
            $self->createPhotoDir($products);
            $self->createPhotosModels($products);
        });
    }

    private function deteleAllPhotosProductsPath()
    {
        $path = \CodeShopping\Models\ProductPhoto::PRODUCTS_PATH;
        \File::deleteDirectory(storage_path($path), true); // true não remover o dir
    }    
    
    private function createPhotoDir(Product $product)
    {
        $path = ProductPhoto::photosPath($product->id);
        \File::makeDirectory($path, 0777, true);
    }

    private function createPhotosModels(Product $product)
    {
        foreach (range(1,5) as $v) {
            $this->createPhotoModel($product);
        }
    }

    private function getFakePhotos(): Collection
    {
        $path = storage_path($this->fakePhotosPath);
        return collect(\File::allFiles($path));
    }

    private function createPhotoModel(Product $product)
    {
        $photo = ProductPhoto::create([
            'product_id' => $product->id,
            'file_name' => 'image.jpg'
        ]);
        $this->generatePhoto($photo);
               
    }


    private function generatePhoto(ProductPhoto $photo)
    {
        $photo->file_name = $this->uploadPhoto($photo->product_id);
        $photo->save();
    }

    private function uploadPhoto($productId): string
    {
        /** @var SpfFileInfo $fotoFile */
        $photoFile = $this->allFakePhotos->random();
        $uploadFile = new \Illuminate\Http\UploadedFile(
            $photoFile->getRealPath(),
            str_random(16). '.' . $photoFile->getExtension()
        );

        ProductPhoto::uploadFiles($productId, [$uploadFile]);

        return $uploadFile->hashName();
    }

}
