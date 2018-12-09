<?php

namespace CodeShopping\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->method() == 'POST') {
            return [
                'password' => 'required|min:4|max:16'
            ];
        }

        if ($this->method() == 'PUT') {
            $password = $this->input('password');
            if (!empty($password) ) {
                return [
                    'password' => 'required|min:4|max:16'
                ];
            }
        }


        $userId = \Auth::guard('api')->user()->id;
        return [
            'name' => 'required|max:255',
            'email' => "required|max:50|email|unique:users,email,{$userId},id"
        ];
    }
}
