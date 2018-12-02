<?php
/**
 * Responsavél pelo envio de mensagem do site
 * User: avdesign
 * Date: 02/12/18
 * Time: 17:01
 */


namespace CodeShopping\Firebase;

use CodeShopping\Models\ChatGroup;


class ChatMenssageFb
{
    use FirebaseSync;

    private $chatGroup;

    public function create(array $data){

        $type = $data['type'];


        switch ($type) {
            case 'audio':
            case 'image':
        }
        $reference = $this->getMessagesReference();
        $reference->push([
            'type' => $data['type'],
            'content' => $data['content'],
            'created_at' => ['.sv' => 'timestamp'], // firebase gera timestamp
            'user_id' => $data['firebase_uid']
        ]);


    }

    private function getMessagesReference()
    {
        $path = "/chat_groups/{$this->chatGroup->id}/messages";
        return $this->getFirebaseDatabase()->getReference($path);

    }


    public function deleteMessages(ChatGroup $chatGroup)
    {
        $this->chatGroup = $chatGroup;
        $this->getMessagesReference()->remove();
    }

}