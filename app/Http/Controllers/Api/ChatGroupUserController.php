<?php

/**
 * Listando membros de um grupo
 * conteudo=5379 a 5381
 */


namespace CodeShopping\Http\Controllers\Api;

use CodeShopping\Http\Resources\ChatGroupUserResource;
use CodeShopping\Http\Requests\ChatGroupUserRequest;
use CodeShopping\Models\ChatGroup;
use CodeShopping\Models\User;

use CodeShopping\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;


class ChatGroupUserController extends Controller
{

    /**
     * @param ChatGroup $chat_group
     * @return mixed
     */
    public function index(ChatGroup $chat_group)
    {
        return new ChatGroupUserResource($chat_group);
    }

    /**
     * @param ChatGroupUserRequest $request
     * @param ChatGroup $chat_group
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ChatGroupUserRequest $request, ChatGroup $chat_group)
    {
        $chat_group->users()->attach($request->users);
        /** @var Collection  $users */
        $users = User::whereIn('id', $request->users)->get();
        return response()->json(new ChatGroupUserResource($chat_group, $users), 201);
    }

    /**
     * @param ChatGroup $chat_group
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ChatGroup $chat_group, User $user)
    {
        $chat_group->users()->detach($user->id);
        return response()->json([], 204);
    }
}
