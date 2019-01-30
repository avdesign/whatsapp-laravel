<?php

namespace CodeShopping\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class ChatGroupInvitation extends Model
{
    use SoftDeletes;

    protected $fillable = ['total', 'expires_at', 'group_id'];
    protected $dates = ['expires_at', 'deleted_at'];

    /**
     * Verifica se tem convite disponível ou não
     * lessThan > | lessThanOrEqualTo >=
     * @return bool
     */
    public function hasInvitation()
    {
        /** @var Carbon $expiresAt */
        $expiresAt = $this->expires_at;
        $expiresAt->hour(23);
        $expiresAt->minute(59);
        $expiresAt->second(59);
        //dd($expiresAt);
        return $this->remaining > 0 &&
            (!$this->expires_at or (new Carbon())->lessThanOrEqualTo($expiresAt));
    }

    public function group()
    {
        return $this->belongsTo(ChatGroup::class, 'group_id');
    }

    public function userInvitations()
    {
        return $this->hasMany(ChatInvitationUser::class, 'invitation_id');
    }
}
