<?php

namespace CodeShopping\Http\Controllers\Api;

use CodeShopping\Http\Requests\PhoneNumberToUpdateRequest;
use CodeShopping\Firebase\Auth as FirebaseAuth;
use CodeShopping\Http\Requests\CustomerRequest;

use CodeShopping\Mail\PhoneNumberChangeMail;
use CodeShopping\Models\User;
use CodeShopping\Models\UserProfile;
use Illuminate\Http\Request;
use CodeShopping\Http\Controllers\Controller;

class CustomerController extends Controller
{
    /**
     * Criação e Autenticação do Customer
     *
     * @param CustomerRequest $request
     * @return array
     */
    public function store(CustomerRequest $request)
    {
        $data = $request->all();
        $token = $request->token;
        $data['phone_number'] = $this->getPhoneNumber($token);
        $data['photo'] = $data['photo']??null; //isset($data['photo']))?$data['photo']:null;
        $user = User::createCustomer($data);
        return [
            'token' => \Auth::guard('api')->login($user)
        ];
    }

    /**
     * Alteração do número do telefone
     *
     * @param PhoneNumberToUpdateRequest $request
     */
    public function requestPhoneNumberUpdate(PhoneNumberToUpdateRequest $request)
    {
        $user =  User::whereEmail($request->get('email'))->first();
        $phoneNumber = $this->getPhoneNumber($request->token);
        $token = UserProfile::createTokenToChangePhoneNumber($user->profile, $phoneNumber);
        // Enviar o e-mail personalizado
        \Mail::to($user)->send(new PhoneNumberChangeMail($user, $token));
        return response()->json([], 204);
    }

    /**
     * Obter número do telefone
     *
     * @param $token
     * @return string
     */
    private function getPhoneNumber($token)
    {
        $firebaseAuth = app(FirebaseAuth::class);
        return $firebaseAuth->phoneNumber($token);

    }

    /**
     * Atualizar número de telefone
     *
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePhoneNumber($token){
        UserProfile::updatePhoneNumber($token);
        return response()->json([], 204);

    }

}
