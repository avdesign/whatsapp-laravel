<?php

namespace CodeShopping\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use CodeShopping\Models\ChatGroup;
use CodeShopping\Models\User;

class ChatMessageFbRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->groupHasUser() || $this->hasSeller();
    }


    private function groupHasUser()
    {
        /** @var ChatGroup $chatGroup */
        $chatGroup = $this->route('chat_group');
        $user = \Auth::guard('api')->user();
        $chatGroup->users()->where('user_id', $user->id)->exists();
    }
    private function hasSeller()
    {
        $user = \Auth::guard('api')->user();
        return $user->role == User::ROLE_SELLER;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required|in:text,image,audio',
            'content' => 'required'
        ];
    }

    /**
     * Subscrever method do FormRequest
     * conteudo=5415
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance();
        $validator->sometimes('content', 'required|string', function ($input) {
            return $input->type === 'text';
        });
        $validator->sometimes('content', 'required|image|max:' . (3 * 2014), function ($input) {
            return $input->type === 'image';
        });
        $validator->sometimes('content', 'required|mimetypes:audio/wav,audio/x-hx-aac-adts|max:' . (3 * 2014), function ($input) {
            return $input->type === 'audio';
        });
        return $validator;
    }
}
