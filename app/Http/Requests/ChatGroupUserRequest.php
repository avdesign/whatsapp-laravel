<?php

namespace CodeShopping\Http\Requests;

use CodeShopping\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChatGroupUserRequest extends FormRequest
{
    /**
     * lms/#/164/147/90/conteudos?capitulo=623&conteudo=5381 a 5381
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Adicionando membros aos grupos
     *
     * @return array
     */
    public function rules()
    {
        $chatGroupId = $this->route('chat_group')->id;
        return [
            'users' => [
                'required',
                Rule::unique('chat_group_user', 'user_id')
                    ->where('chat_group_id', $chatGroupId),
                Rule::exists('users', 'id')
                    ->where('role', User::ROLE_CUSTOMER),
            ]
        ];
    }

    public function messages()
    {
        return [
            'exists' => 'Não foi encontrado ou precisa ser um vendedor.'
        ];
    }
}