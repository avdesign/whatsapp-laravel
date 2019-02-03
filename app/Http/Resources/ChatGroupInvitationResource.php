<?php

namespace CodeShopping\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatGroupInvitationResource extends JsonResource
{
    private $isCollection;

    public function __construct($resource, $isCollection = false)
    {
        parent::__construct($resource);
        $this->isCollection = $isCollection;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // android e ios
        $link = env('MOBILE_PAGE_LINK')
            . '?link='. env('MOBILE_URL'). '/' .$this->slug
            . '&apn='. env('MOBILE_ID')
            . '&ibi='. env('MOBILE_ID');
        /*
        // só android
        $link = env('MOBILE_PAGE_LINK')
            . '?link='. env('MOBILE_URL'). '/' .$this->slug
            . '&ibi='. env('MOBILE_ID');
        */
        $data = [
            'id' => $this->id,
            'total' => (int)$this->total,
            'remaining' => (int)$this->remaining,
            'link' => $link,
            'expires_at' => $this->expires_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        if (!$this->isCollection) {
            $data['group'] = new ChatGroupResource($this->group);
        }
        return $data;
    }
}
