<?php
declare(strict_types=1);

namespace CodeShopping\Models;


use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use CodeShopping\Firebase\FirebaseSync;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\UploadedFile;

class ChatGroup extends Model
{
    use SoftDeletes, FirebaseSync, PivotEventTrait;

    const BASE_PATH = 'app/public';
    const DIR_CHAT_GROUPS = 'chat_groups';
    const CHAT_GROUP_PHOTO_PATH = self::BASE_PATH . '/' . self::DIR_CHAT_GROUPS;

    protected $fillable = ['name', 'photo'];
    protected $dates = ['deleted_at'];

    /**
     * Criar Gruo: photo obrigatória
     *
     * @param array $data
     * @return ChatGroup
     * @throws \Exception
     */
    public static function createWithPhoto(array $data): ChatGroup
    {
        try {
            self::uploadPhoto($data['photo']);
            $data['photo'] = $data['photo']->hashName();
            \DB::beginTransaction();
            $chatGroup = self::create($data);
            \DB::commit();
        } catch (\Exception $e) {
            self::deleteFile($data['photo']);
            \DB::rollBack();
            throw $e;
        }
        return $chatGroup;
    }


    public function updateWithPhoto(array $data): ChatGroup
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
        } catch (\Exception $e) {
            if (isset($data['photo'])) {
                self::deleteFile($data['photo']);
            }
            \DB::rollBack();
            throw $e;
        }
        return $this;
    }



    /**
     * Alterar nome ou foto do grupo
     *
     * @param array $data
     * @return ChatGroup
     * @throws \Exception
     */
    public function updateWithPhoto2(array $data): ChatGroup
    {
        try {
            if (isset($data['photo'])) {
                self::uploadPhoto($data['photo']);
                $this->deletePhoto();
                $data['photo'] = $data['photo']->hashName();
            } else {
                unset($data['photo']);
            }
            \DB::beginTransaction();
            //dd($this->fill($data));
            $this->fill($data)->save();
            \DB::commit();
        } catch (\Exception $e) {
            if (isset($data['photo'])) {
                self::deleteFile($data['photo']);
            }
            \DB::rollBack();
            throw $e;
        }
        return $this;
    }

    private static function uploadPhoto(UploadedFile $photo)
    {
        $dir = self::photoDir();
        $photo->store($dir, ['disk' => 'public']);
    }

    private static function deleteFile(UploadedFile $photo)
    {
        $path = self::photoPath();
        $photoPath = "{$path}/{$photo->hashName()}";
        if (file_exists($photoPath)) {
            \File::delete($photoPath);
        }
    }

    private function deletePhoto(){
        $dir = self::photoDir();
        \Storage::disk('public')->delete("{$dir}/{$this->photo}");
    }


    private static function photoPath()
    {
        $path = self::CHAT_GROUP_PHOTO_PATH;
        return storage_path($path);
    }

    private static function photoDir()
    {
        $dir = self::DIR_CHAT_GROUPS;
        return $dir;
    }


    /**
     * Relacionamento muitos para muitos
     *
     * @return BelongsToMany
     */
    public function users(){
        return $this->belongsToMany(User::class);
    }

    /**
     * Relacionamento dos convites para o grupo
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function linkInvitations()
    {
        return $this->hasMany(ChatGroupInvitation::class, 'group_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Sincronia com o Firebase
    |--------------------------------------------------------------------------
    |
    | syncFbRemove() - faz uma atualização sobrescrevendo o método tsyncFbSet()
    | syncFbSet() - remove o campo photo no BD Firebase e passa os dados da referência photo_url
    | getPhotoUrlAttribute() - retorna a url completa da foto
    | getPhotoUrlBaseAttribute() - acrescenta a base e não a url completa da foto
    |
    */
    protected function syncFbRemove()
    {
        $this->syncFbSet();
    }

    public function updateInFb()
    {
        $this.$this->syncFbSet(self::$OPERATION_UPDATE);
    }

    protected function syncFbSet($operation = null)
    {
        $data = $this->toArray();
        $data['photo_url'] = $this->photo_url_base;
        unset($data['photo']);
        $this->setTimestamps($data, $operation);
        // para alterar os campos usar set($data) para não fazer manual
        $this->getModelReference()->set($data);
    }

    public function getPhotoUrlAttribute()
    {
        return asset("storage/{$this->photo_url_base}");
    }

    public function getPhotoUrlBaseAttribute()
    {
        $path = self::photoDir();
        return "{$path}/{$this->photo}";
    }

    /**
     * Enviar relacionamentos para o Firebase
     *
     * @param $model
     * @param $relationName
     * @param $pivotIds
     * @param $pivotIdsAttribute
     */
    protected function syncPivotAttached($model, $relationName, $pivotIds, $pivotIdsAttribute)
    {
        $users = User::whereIn('id', $pivotIds)->get();
        $data = [];
        foreach ($users as $user) {
            $data["chat_groups_users/{$model->id}/{$user->profile->firebase_uid}"] = true;
        }
        $this->getFirebaseDatabase()->getReference()->update($data);
    }

    /**
     * Sincronizando exclusão dos membros de um grupo
     *
     * @param $model
     * @param $relationName
     * @param $pivotIds
     */
    protected function syncPivotDetached($model, $relationName, $pivotIds)
    {
        $users = User::whereIn('id', $pivotIds)->get();
        $data = [];
        foreach ($users as $user) {
            $data["chat_groups_users/{$model->id}/{$user->profile->firebase_uid}"] = null;
        }
        //remove multipols usuários
        $this->getFirebaseDatabase()->getReference()->update($data);
        // remove só um usuário
        //$this->getFirebaseDatabase()->getReference()->remove();
    }


}