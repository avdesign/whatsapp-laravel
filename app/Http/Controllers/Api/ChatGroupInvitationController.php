<?php

namespace CodeShopping\Http\Controllers\Api;

use CodeShopping\Http\Resources\ChatGroupInvitationCollection;
use CodeShopping\Http\Resources\ChatGroupInvitationResource;
use CodeShopping\Http\Requests\ChatGroupInvitationRequest;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Models\ChatGroupInvitation;
use CodeShopping\Models\ChatGroup;

class ChatGroupInvitationController extends Controller
{

    public function index(ChatGroup $chat_group)
    {
        $linkInvitations = $chat_group->linkInvitations()->paginate(5);
        return new ChatGroupInvitationCollection($linkInvitations, $chat_group);

    }


    public function store(ChatGroupInvitationRequest $request, ChatGroup $chat_group)
    {
        $chatGroup = ChatGroupInvitation::create(
            $request->all()+['group_id' => $chat_group->id]
        );
        return new ChatGroupInvitationResource($chatGroup);
    }


    public function show(ChatGroup $chat_group, ChatGroupInvitation $link_invitation)
    {
        $this->assertInvitation($chat_group, $link_invitation);
        return new ChatGroupInvitationResource($link_invitation);
    }


    public function update(ChatGroupInvitationRequest $request, ChatGroup $chat_group, ChatGroupInvitation $link_invitation)
    {
        $this->assertInvitation($chat_group, $link_invitation);
        $link_invitation
            ->fill($request->except('group_id'))
            ->save();
        return new ChatGroupInvitationResource($link_invitation);
    }

    public function destroy(ChatGroup $chat_group, ChatGroupInvitation $link_invitation)
    {

        $this->assertInvitation($chat_group, $link_invitation);
        $link_invitation->delete();

        return response()->json([], 204);
    }

    private function assertInvitation(ChatGroup $chatGroup, ChatGroupInvitation $link_invitation)
    {
        if ($link_invitation->group_id != $chatGroup->id) {
            abort(404);
        }
    }
}
