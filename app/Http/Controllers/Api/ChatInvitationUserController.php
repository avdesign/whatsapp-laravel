<?php

namespace CodeShopping\Http\Controllers\Api;

use CodeShopping\Http\Resources\ChatInvitationUserCollection;
use CodeShopping\Exceptions\ChatInvitationUserException;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Resources\ChatInvitationUserResource;
use CodeShopping\Models\ChatGroupInvitation;
use CodeShopping\Models\ChatInvitationUser;
use CodeShopping\Models\ChatGroup;

use Illuminate\Http\Request;

class ChatInvitationUserController extends Controller
{
    /**
     * Relacionamento hasManyThrough
     * Pega o id através de outro model
     * -> chat_group
     *  -> chat_group_invitation
     *   -> chat_invitation_users
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ChatGroup $chat_group)
    {
        $userInvitations = $chat_group->userInvitations()->with('user')->paginate();

        return new ChatInvitationUserCollection($userInvitations, $chat_group);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ChatGroupInvitation $invitation_slug)
    {
        try {
            $invitationUser = ChatInvitationUser::createIfAllowed($invitation_slug, \Auth::guard('api')->user());
        } catch (ChatInvitationUserException $e) {
            switch ($e->getCode()){
                case ChatInvitationUserException::ERROR_NOT_INVITATION:
                case ChatInvitationUserException::ERROR_IS_MEMBER:
                case ChatInvitationUserException::ERROR_HAS_STORED:
                    return abort(403, $e->getMessage());
                case ChatInvitationUserException::ERROR_HAS_SELLER:
                    return abort(422, $e->getMessage());
            }

        }
    }

    public function show(ChatGroup $chat_group, ChatInvitationUser $invitation)
    {
        $this->assertInvitation($chat_group, $invitation);

        return new ChatInvitationUserResource($invitation);
    }

    public function edit(ChatInvitationUser $chatInvitationUsers)
    {
        //
    }

    public function update(Request $request, ChatGroup $chat_group, ChatInvitationUser $invitation)
    {
        $this->assertInvitation($chat_group, $invitation);

        if ($invitation->status != ChatInvitationUser::STATUS_PENDING) {
            abort(403, 'Só é possível alterar convite com status pendente.');
        }

        $this->validate($request,[
            'status' => 'required|in:'.
                ChatInvitationUser::STATUS_APPROVED.','.
                ChatInvitationUser::STATUS_REPROVED
        ]);

        $invitation->status = $request->get('status');
        $invitation->save();

        return new ChatInvitationUserResource($invitation);
    }

    public function assertInvitation(ChatGroup  $chatGroup, ChatInvitationUser $userInvitation)
    {
        if ($userInvitation->invitation->group_id != $chatGroup->id) {
            abort(404);
        }
    }
}
