<?php
declare(strict_types=1);

namespace CodeShopping\Events;


use CodeShopping\Models\ChatGroup;
use CodeShopping\Models\User;

class ChatMessageSent
{
    private $chatGroup;
    private $messageType;
    private $content;
    private $from;

    /**
     * Event para enviar mensagens aos membros do grupo.
     *
     * @param ChatGroup $chatGroup
     * @param $messageType
     * @param $content
     * @param User $from
     */
    public function __construct(ChatGroup $chatGroup, $messageType, $content, User $from)
    {
        $this->chatGroup = $chatGroup;
        $this->messageType = $messageType;
        $this->content = $content;
        $this->from = $from;
    }

    /**
     * @return ChatGroup
     */
    public function getChatGroup()
    {
        return $this->chatGroup;
    }

    /**
     * @return mixed
     */
    public function getMessageType()
    {
        return $this->messageType;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return User
     */
    public function getFrom()
    {
        return $this->from;
    }






}
