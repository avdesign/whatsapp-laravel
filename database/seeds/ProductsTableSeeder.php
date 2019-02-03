<?php
declare(strict_types=1);


use Illuminate\Database\Seeder;
use CodeShopping\Models\Product;
use CodeShopping\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;


class ProductsTableSeeder extends Seeder
{
    private $allFakerPhotos;
    private $fakerPhotoPath = 'app/faker/product_photos';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Consultar as categorias
        /** @var \Illuminate\Database\Eloquent\Collection $categories */
        $categories = Category::all();
        $this->allFakerPhotos = $this->getFakerPhotos();
        $this->deleteAllPhotosInProductPath();
        factory(Product::class, 30)
        ->make()
        ->each(function(Product $product) use($categories){
            $product = Product::createWithPhoto($product->toArray() + [
                    'photo' => $this->getUploadedFile()
                ]);

            $categoryId = $categories->random()->id;
            $product->categories()->attach($categoryId);
        });
    }

    private  function deleteAllPhotosInProductPath()
    {
        $path = Product::PRODUCTS_PATH;
        \File::deleteDirectory(storage_path($path), true);
    }

    private function getFakerPhotos(): Collection
    {
        $path = storage_path($this->fakerPhotoPath);
        return collect(\File::allFiles($path));
    }

    private function getUploadedFile(): UploadedFile
    {
        /** @var SplFileInfo $photoFile */
        $photoFile = $this->allFakerPhotos->random();
        $uploadFile = new UploadedFile(
            $photoFile->getRealPath(),
            str_random(16) . '.' . $photoFile->getExtension()
        );
        //upload da photo
        return $uploadFile;
    }

}
