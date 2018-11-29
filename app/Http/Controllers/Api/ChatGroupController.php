<?php

namespace CodeShopping\Http\Controllers\Api;

use CodeShopping\Http\Requests\ChatGroupRequest;
use CodeShopping\Http\Resources\ChatGroupResource;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Models\ChatGroup;

class ChatGroupController extends Controller
{
    /**
     * Pode ser usado: withCount('name') ou withCount(['nome1,nomwe2])
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $chat_groups = ChatGroup::orderBy('id', 'desc')->withCount('users')->paginate(5);
        return ChatGroupResource::collection($chat_groups);
    }


    public function store(ChatGroupRequest $request)
    {
        $chat_groups = ChatGroup::createWithPhoto($request->all());
        return new ChatGroupResource($chat_groups);
    }

    public function show(ChatGroup $chat_group)
    {
        return new ChatGroupResource($chat_group);
    }


    public function update(ChatGroupRequest $request, ChatGroup $chatGroup)
    {
        $chatGroup->updateWithPhoto($request->all());
        return new ChatGroupResource($chatGroup);
    }

    public function destroy(ChatGroup $chatGroup)
    {
        $chatGroup->delete();
        return response()->json([], 204);
    }
}
