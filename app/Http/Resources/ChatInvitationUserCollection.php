<?php

namespace CodeShopping\Http\Resources;

use CodeShopping\Models\ChatGroup;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChatInvitationUserCollection extends ResourceCollection
{

    /**
     * @var ChatGroup
     */
    private $group;

    public function __construct($resource, ChatGroup $group)
    {
        $this->group = $group;
        parent::__construct($resource);

    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'group' => new ChatGroupResource($this->group),
            'invitations' => $this->collection->map(function ($invitation) {
                return new ChatInvitationUserResource($invitation, true);
            })
        ];
    }
}
