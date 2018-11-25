<?php

namespace CodeShopping\Http\Controllers\Api;

use CodeShopping\Http\Requests\UserProfileUpdateRequest;
use CodeShopping\Firebase\Auth as FirebaseAuth;
use CodeShopping\Http\Resources\UserResource;
use CodeShopping\Http\Controllers\Controller;

class UserProfileController extends Controller
{
    /**
     * @param Request $request
     * @return array
     */
    public function update(UserProfileUpdateRequest $request)
    {
        $data = $request->all();
        if ($request->has('token')) {
            $token = $request->token;
            $data['phone_number'] = $this->getPhoneNumber($token);
        }

        if ($request->has('remove_photo')) {
            $data['photo'] = null;
        }
        $user = \Auth::guard('api')->user();
        $user->updateWithProfile($data);
        // Cria um novo token para ler as informações da alteração
        $resource = new userResource($user);
        $newToken = \Auth::guard('api')->login($user);
        return [
            'user' => $resource->toArray($request),
            'token' => $newToken
        ];
    }

    private function getPhoneNumber($token)
    {
        $firebaseAuth = app(FirebaseAuth::class);
        return $firebaseAuth->phoneNumber($token);
    }
}