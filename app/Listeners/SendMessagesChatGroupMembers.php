<?php
declare(strict_types=1);


namespace CodeShopping\Listeners;


use CodeShopping\Firebase\CloudMessagingFb;
use CodeShopping\Events\ChatMessageSent;
use CodeShopping\Models\UserProfile;
use Illuminate\Support\Collection;
use CodeShopping\Models\User;


class SendMessagesChatGroupMembers
{
    /**
     * @var ChatMessageSent
     */
    private $event;
    /**
     * Enviar mensagens para os membros do grupo.
     * 164/147/90/conteudos?capitulo=630&conteudo=5490
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ChatMessageSent  $event
     * @return void
     */
    public function handle(ChatMessageSent $event)
    {
        $this->event = $event;
        $tokens = $this->getTokens();
        // Continuar se tiver tokens
        if (!count($tokens)) {
            return;
        }

        $from = $this->event->getFrom();
        $chatGroup = $this->event->getChatGroup();
        /** @var CloudMessagingFb $messaging */
        $messaging = app(CloudMessagingFb::class);
        $messaging
            ->setTitle("{$from->name} enviou uma mensagem em {$chatGroup->name}")
            ->setBody($this->getBody())
            ->setTokens($tokens)
            ->send();

    }

    /**
     * Tokens dos vendedores e clientes
     *
     * @return array
     */
    private function getTokens(): array
    {
       $menbersTokens = $this->getMembersTokens();
       $sellersTokens = $this->getSellersTokens();

       return array_merge($menbersTokens, $sellersTokens);

    }

    private function getMembersTokens(): array
    {
        $chatGroup = $this->event->getChatGroup();
        //quem enviou as mensagens
        $from = $this->event->getFrom();

        /** @var Collection $users */
        $users = $chatGroup->users()->whereHas('profile', function ($query) use($from) {//pego o relacionamento de users->profile
            $query
                ->whereNotNull('device_token') // não pego os campos null
                ->whereNotIn('id', [$from->profile-id]) // Não pego o usuário que enviou
                ->get();
        });

        /** @var Collection $membersTokensCollection */
        $membersTokensCollection = $users->map(function ($user)  { // (map) retorna uma collection dos tokens
            return $user->profile->device_token;
        });

        return $membersTokensCollection->toArray();
    }

    private function getSellersTokens(): array
    {
        //quem enviou as mensagens
        $from = $this->event->getFrom();
        $sellersTokensCollection = UserProfile::whereNotNull('device_token')
            ->whereNotIn('id', [$from->profile->id])
            ->whereHas('user', function($query){
                $query->where('role', User::ROLE_SELLER);
            })
            ->get()
            ->pluck('device_token');

        return $sellersTokensCollection->toArray();
    }


    private function getBody()
    {
        switch ($this->event->getMessageType()){
            case 'text':
                return substr($this->event->getContent(), 0,20);
            case 'audio':
                return 'Novo Audio';
            case 'image':
                return 'Nova Imagem';
        }
    }
}
