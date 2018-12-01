<?php
declare(strict_types=1);

namespace CodeShopping\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Mnabialek\LaravelEloquentFilter\Traits\Filterable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use CodeShopping\Firebase\FirebaseSync;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, SoftDeletes, Filterable, FirebaseSync;

    const ROLE_SELLER = 1;
    const ROLE_CUSTUMER = 2;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Envolve a criação do usuário, perfil e upload de fotos
     * Upload de fotos é uma responsabilidade do UserProfile
     * @param array $data
     * @return User
     * @throws \Exception
     */
    public static function createCustomer(array $data): User
    {
        try{
            UserProfile::uploadPhoto($data['photo']);
            \DB::beginTransaction();
            $user = self::createCustomerUser($data);
            UserProfile::saveProfile($user, $data);
            \DB::commit();
        } catch (\Exception $e) {
            UserProfile::deleteFile($data['photo']);
            \DB::rollBack();
            throw $e;
        }
        return $user;
    }

    /**
     * Criar usuário tipo: customer
     *
     * @param $data
     * @return User
     */
    private static function createCustomerUser($data): User
    {
        $data['password'] = bcrypt(str_random(16));
        $user = User::create($data);
        $user->role = User::ROLE_CUSTUMER;
        $user->save();
        return $user;
    }

    /**
     * Atualização do perfil dos vendedores e clientes
     * @param array $data
     * @return User
     * @throws \Exception
     */
    public function updateWithProfile(array $data): User
    {
        try {
            //upload da photo
            //photo = null - remover photo
            //sem photo no array - não faz nada
            if (isset($data['photo'])) {
                UserProfile::uploadPhoto($data['photo']);
            }
            \DB::beginTransaction();
            $this->fill($data);
            $this->save();
            UserProfile::saveProfile($this, $data);
            \DB::commit();
        } catch (\Exception $e) {
            if (isset($data['photo'])) {
                UserProfile::deleteFile($data['photo']);
            }
            \DB::rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Se o password estiver no campo aplica a criptografia automáticamente
     *
     * @param array $attributes
     * @return $this
     */
    public function fill(array $attributes)
    {
        !isset($attributes['password']) ?: $attributes['password'] = bcrypt($attributes['password']);
        return parent::fill($attributes);
    }

    /**
     * JWTAuth Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->id;
    }

    /**
     *
     * JWTAuth Return a key value array, contendo os dados do usuário
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
            'profile' => [
                'has_photo' => $this->profile->photo ? true : false,
                'photo_url' => $this->profile->photo_url,
                'phone_number' => $this->profile->phone_number
            ]
        ];
    }


    /**
     * Relacionamento hasOne
     * @return $this
     */
    public function profile()
    {
        //withDefault, utiliza o pattern NullPattern, pois o perfil pode não ter registro.
        //o withDefault, nos devolve uma instância vazia de user padrão, mesmo que não exista um perfil
        return $this->hasOne(UserProfile::class)->withDefault();
    }

    /*
    |--------------------------------------------------------------------------
    |  Sobrescrever syncFbSet()
    |--------------------------------------------------------------------------
    | syncFbSetCustom() -
    | photo_url_base - UserProfile getPhotoUrlBaseAttribute()
    |
    */
    protected function syncFbCreate()
    {
        $this->syncFbSetCustom();
    }

    protected function syncFbUpdate()
    {
        $this->syncFbSetCustom();
    }

    protected function syncFbRemove()
    {
        $this->syncFbSetCustom();
    }

    public function syncFbSetCustom()
    {

        $this->profile->refresh(); // Atualiza o profile antes
        if ($this->profile->firebase_uid) {
            $database = $this->getFirebaseDatabase();
            $path = 'users/'. $this->profile->firebase_uid;
            $reference = $database->getReference($path);
            $reference->set([
                'name' => $this->name,
                'photo_url' => $this->profile->photo_url_base,
                'deleted_at' => $this->deleted // para saber se o user já foi excuido
            ]);
        }
    }


}
