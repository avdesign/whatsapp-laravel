<?php

namespace CodeShopping\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatGroupRequest extends FormRequest
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

        //dd($this->input('photo'));


        if ($this->method() == 'POST') {
            return [
                'name' => 'required|max:255',
                'photo' => 'required|image|max:' . (3 * 1024)
            ];
        }

        if ($this->method() == 'PUT') {
            if ( $this->input('photo')) {
                return [
                    'name' => 'required|max:255',
                    'photo' => 'image|max:' . (3 * 1024)
                ];
            } else {
                return [
                    'name' => 'required|max:255'
                ];
            }
        }
    }
}
