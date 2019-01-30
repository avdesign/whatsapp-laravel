<?php

namespace CodeShopping\Models;

use CodeShopping\Exceptions\ChatInvitationUserException;
use Illuminate\Database\Eloquent\Model;


class ChatInvitationUser extends Model
{
    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REPROVED = 3;

    protected $fillable = ['invitation_id', 'user_id'];

    // Criar só se for permitido
    public static function createIfAllowed(ChatGroupInvitation $groupInvitation, User $user){
        //Lançar exceção caso não permitido
        self::throwIfNotAllowed($groupInvitation, $user);

        return self::create([
            'invitation_id' => $groupInvitation->id,
            'user_id' => $user->id
        ]);
    }

    // Lançar exceção caso não permitido
    public static function throwIfNotAllowed(ChatGroupInvitation $groupInvitation, User $user){
        if (!$groupInvitation->hasInvitation()){
            throw new ChatInvitationUserException(
               "Ingresso no grupo não é permitido", ChatInvitationUserException::ERROR_NOT_INVITATION
            );
        }

        if ($user->role == User::ROLE_SELLER){
            throw new ChatInvitationUserException(
                "Vendedor não precisa ingressar em um grupo.", ChatInvitationUserException::ERROR_HAS_SELLER
            );
        }

        //Verifica se já é membro do grupo
        if (self::isMember($groupInvitation->group, $user)) {
            throw new ChatInvitationUserException(
                "Usuário já é membro deste grupo.", ChatInvitationUserException::ERROR_IS_MEMBER
            );
        }

        //Se existe alguma requisição de entrada cadastrada
        if (self::hasStored($groupInvitation, $user)) {
            throw new ChatInvitationUserException(
                "Usuário já cadastrou um convite.", ChatInvitationUserException::ERROR_HAS_STORED
            );
        }

    }

    //Verifica se já é membro do grupo
    private static function isMember(ChatGroup $chatGroup, User $user)
    {
        return $chatGroup->users()->where('id', $user->id)->exists();
    }

    //Se existe alguma requisição de entrada cadastrada
    private static function hasStored(ChatGroupInvitation $groupInvitation, User $user)
    {
        return $groupInvitation->userInvitations()->where('user_id', $user->id)->exists();
    }



    public function invitation()
    {
        return $this->belongsTo(ChatGroupInvitation::class, 'invitation_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
