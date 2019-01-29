<?php

namespace CodeShopping\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatGroupInvitationRequest extends FormRequest
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
        if ($this->method() == 'POST'){
            return [
                'total' => 'required|integer|min:1',
                'expires_at' => 'nullable|date|after_or_equal:today'
            ];
        }


        if ($this->method() == 'PUT'){
            $invitation = $this->route('link_invitation');
            return [
                'total' => 'required|integer|min:' .$invitation->remaining,
                'expires_at' => 'nullable|date|after_or_equal:today'
            ];
        }

    }
}
