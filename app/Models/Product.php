<?php
declare(strict_types=1);


namespace CodeShopping\Models;

use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mnabialek\LaravelEloquentFilter\Traits\Filterable;

class Product extends Model
{
    use Sluggable, SoftDeletes, Filterable;

    protected $dates = ['deleted_at'];
    protected $fillable = ['name', 'description', 'price', 'active', 'photo'];

    const BASE_PATH = 'app/public';
    const DIR_PRODUCTS = 'products';

    const PRODUCTS_PATH = self::BASE_PATH . '/' . self::DIR_PRODUCTS;

    public static function createWithPhoto(array $data): Product
    {
        try{
            self::uploadPhoto($data['photo']);
            \DB::beginTransaction();
            $data['photo'] = $data['photo']->hashName();
            $product = self::create($data);
            \DB::commit();
            return $product;
        }catch(\Exception $e){
            self::deleteFile($data['photo']);
            \DB::rollBack();
            throw $e;
        }
    }


    public function updateWithPhoto(array $data): Product
    {
        try {
            if (isset($data['photo'])) {
                self::uploadPhoto($data['photo']);
                $this->deletePhoto();
                $data['photo'] = $data['photo']->hashName();
            }
            \DB::beginTransaction();
            $this->fill($data)->save();
            \DB::commit();
            return $this;
        } catch (\Exception $e) {
            if (isset($data['photo'])) {
                self::deleteFile($data['photo']);
            }
            \DB::rollBack();
            throw $e;
        }
    }

    private function deletePhoto()
    {
        $dir = self::photoDir();
        \Storage::disk('public')->delete("{$dir}/{$this->photo}");
    }

    public static function deleteFile(UploadedFile $photo)
    {
        $path = self::photoPath();
        $photoPath = "{$path}/{$photo->hashName()}";
        if (fileExists($photoPath)) {
            \File::delete($photoPath);
        }
    }

    public static function photoPath(){
        $path = self::PRODUCTS_PATH;
        return storage_path($path);
    }

    public static function uploadPhoto(UploadedFile $photo){
        $dir = self::photoDir();
        $photo->store($dir, ['disk' => 'public']);
    }

    public function getPhotoUrlAttribute()
    {
        return asset("storage/{$this->photo_url_without_asset}");
    }

    public function getPhotoUrlWithoutAssetAttribute()
    {
        $path = self::photoDir();
        return "{$path}/{$this->photo}";
    }

    public static function photoDir()
    {
        $dir = self::DIR_PRODUCTS;


        return $dir;
    }


    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }


    public function categories(){
        return $this->belongsToMany(Category::class);
    }

    public function photos(){
        return $this->hasMany(ProductPhoto::class);
    }

    
}

