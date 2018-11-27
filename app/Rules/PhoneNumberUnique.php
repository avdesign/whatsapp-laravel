<?php

namespace CodeShopping\Rules;

use CodeShopping\Firebase\Auth as FirebaseAuth;
use CodeShopping\Models\UserProfile;
use Illuminate\Contracts\Validation\Rule;

class PhoneNumberUnique implements Rule
{
    /**
     * @var null
     */
    private $ignoredUserId;

    /**
     * Create a new rule instance.
     * @param null $ignoredUserId
     */
    public function __construct($ignoredUserId = null)
    {
        $this->ignoredUserId = $ignoredUserId;
    }

    /**
     * Verifica se esse telefone já não existe.
     *
     * @param  string $attribute
     * @param $tokenValue
     * @return bool
     */
    public function passes($attribute, $tokenValue)
    {
        $firebaseAuth = app(FirebaseAuth::class);
        try {
            $phoneNumber = $firebaseAuth->phoneNumber($tokenValue);
            $query = (new UserProfile)->where('phone_number', $phoneNumber);
            $profile = $query->first();
            $checkedIgnoreduserId = $this->ignoredUserId != null && $this->ignoredUserId == $profile->user->id;
            return $profile == null || $checkedIgnoreduserId;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Mensagem de retorno.
     *
     * @return string
     */
    public function message()
    {
        return 'O número de telefone já existe em nosso sistema';
    }
}
