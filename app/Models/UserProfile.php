<?php
declare(strict_types=1);

namespace CodeShopping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class UserProfile extends Model
{
    const BASE_PATH = 'app/public';
    const DIR_USERS = 'users';
    const DIR_USER_PHOTO = self::DIR_USERS . '/photos';
    const USER_PHOTO_PATH = self::BASE_PATH . '/' . self::DIR_USER_PHOTO;

    protected $fillable = ['phone_number', 'photo'];


    /**
     * Obter token para alteração do número do telefone.
     *
     * @param UserProfile $profile
     * @param $phoneNumber
     * @return string
     */
    public static function createTokenToChangePhoneNumber(UserProfile $profile, $phoneNumber): string
    {
       $token = base64_encode($phoneNumber);
       $profile->phone_number_token_change = $token;
       $profile->save();
       return $token;

    }

    /**
     * Faz atualização do número do telefone
     *
     * @param $token
     * @return UserProfile
     */
    public static function updatePhoneNumber($token): UserProfile
    {
        $profile = UserProfile::where('phone_number_token_change', $token)->firstOrFail();
        $phoneNumber = base64_decode($token);
        $profile->phone_number = $phoneNumber;
        $profile->phone_number_token_change = null;
        $profile->save();



        return $profile;
    }


    /**
     * Salvar ou Editar perfil do usuário
     *
     * @param User $user
     * @param array $data
     * @return UserProfile
     */
    public static function saveProfile(User $user, $data = array()): UserProfile
    {
        // caso exista $data['photo'] - exclui a photo
        if (array_key_exists('photo', $data)) {
            self::deletePhoto($user->profile);
            $data['photo'] = UserProfile::getPhotoHashName($data['photo']);
        }
        $user->profile->fill($data)->save();
        return $user->profile;
    }

    /**
     * Criar nome aleatório para a photo
     *
     * @param UploadedFile|null $photo
     * @return null|string
     */
    private static function getPhotoHashName(UploadedFile $photo = null)
    {
        return $photo ? $photo->hashName() : null;
    }

    /**
     * Caminho absoluto da pasta das fotos do usuário
     *
     * @return string
     */
    public static function photoPath()
    {
        $path = self::USER_PHOTO_PATH;
        return storage_path($path);
    }

    /**
     * Upload de apenas uma photo, não é obrigatória
     *
     * @param UploadedFile|null $photo
     */
    public static function uploadPhoto(UploadedFile $photo = null)
    {
        if (!$photo) {
            return;
        }
        $dir = self::photoDir();
        $photo->store($dir, ['disk' => 'public']);
    }

    /**
     * Excluir a photo na criação do usuário
     *
     * @param UploadedFile|null $photo
     */
    public static function deleteFile(UploadedFile $photo = null)
    {
        if (!$photo) {
            return;
        }
        $path = self::photoPath();
        $photoPath = "{$path}/{$photo->hashName()}";
        if (file_exists($photoPath)) {
            \File::delete($photoPath);
        }
    }

    /**
     * Excluir photo na atualização o perfil
     *
     * @param UserProfile $profile
     */
    private static function deletePhoto(UserProfile $profile)
    {
        if (!$profile->photo) {
            return;
        }
        $dir = self::photoDir();
        \Storage::disk('public')->delete("{$dir}/{$profile->photo}");
    }

    public static function photoDir()
    {
        $dir = self::DIR_USER_PHOTO;
        return $dir;
    }

    /**
     * Retorna o url da photo ou gravatar
     *
     * @return string
     */
    public function getPhotoUrlAttribute()
    {
        $path = self::photoDir();
        return $this->photo ? asset("storage/{$path}/{$this->photo}"):
            'https://www.gravatar.com/avatar/nouser.jpg';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
