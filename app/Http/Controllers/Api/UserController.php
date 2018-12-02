<?php

namespace CodeShopping\Http\Controllers\Api;

use CodeShopping\Http\Filters\UserFilter;
use CodeShopping\Http\Resources\UserResource;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Requests\UserRequest;
use CodeShopping\Events\UserCreatedEvent;
use CodeShopping\Common\OnlyTrashed;
use CodeShopping\Models\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;


class UserController extends Controller
{
    use OnlyTrashed;
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        /** @var UserFilter $filter */
        $filter = app(UserFilter::class);
        $query = User::query();
        $query = $this->onlyTrashedIfRequested($request, $query);
        /** @var Builder $filterQuery */
        $filterQuery = User::filtered($filter);
        $users = $filter->hasFilterParameter() ?
            $filterQuery->get() :
            $filterQuery->paginate(10);

        return UserResource::collection($users);
    }

    /**
     * @param UserRequest $request
     * @return UserResource
     */
    public function store(UserRequest $request)
    {
        $user = User::create($request->all());
        event(new UserCreatedEvent($user));
        return new UserResource($user);
    }

    /**
     * @param User $user
     * @return UserResource
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * @param UserRequest $request
     * @param User $user
     * @return UserResource
     */
    public function update(UserRequest $request, User $user)
    {
        $user->fill($request->all());
        $user->save();
        return new UserResource($user);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([], 204);
    }
}
