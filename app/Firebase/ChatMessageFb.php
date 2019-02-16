<?php
/**
 * Responsavél pelo envio de mensagem do site
 * User: avdesign
 * Date: 02/12/18
 * Time: 17:01
 */


namespace CodeShopping\Firebase;

use CodeShopping\Events\ChatMessageSent;
use CodeShopping\Models\ChatGroup;
use Illuminate\Http\UploadedFile;


class ChatMessageFb
{
    use FirebaseSync;

    private $chatGroup;

    public function create(array $data){

       // dd($data);

        $this->chatGroup = $data['chat_group'];
        $type = $data['type'];

        switch ($type) {
            case 'audio':
            case 'image':
                $this->upload($data['content']);
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $data['content'];
                $fileUrl = $this->groupFilesDir() . '/' . $this->buildFileName($uploadedFile);
                $data['content'] = $fileUrl;
        }
        $reference = $this->getMessagesReference();
        $newReference = $reference->push([
            'type' => $data['type'],
            'content' => $data['content'],
            'created_at' => ['.sv' => 'timestamp'], // firebase gera timestamp
            'user_id' => $data['user']->profile->firebase_uid
        ]);

        $this->setLastMessage($newReference->getKey());

        $this->chatGroup->updateInFb();

        //Não enviar as msgs no console e no teste unitário
        if (!app()->runningInConsole() && !app()->runningUnitTests()) {
            event( new ChatMessageSent($this->chatGroup,$data['type'],$data['content'],$data['user']));
        }
    }

    /**
     * @param UploadedFile $file
     */
    private function upload(UploadedFile $file)
    {
        $file->storeAs($this->groupFilesDir(), $this->buildFileName($file), ['disk' => 'public']);
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    private function buildFileName(UploadedFile $file)
    {
        switch ($file->getMimeType()){
            case 'audio/x-hx-aac-adts':
                return "{$file->hashName()}aac";
            default:
                return $file->hashName();
        }
    }


    /**
     * Pasta onde salvar os files
     * @return string
     */
    private function groupFilesDir()
    {
        return ChatGroup::DIR_CHAT_GROUPS . '/' . $this->chatGroup->id . '/messages_files';
    }


    public function deleteMessages(ChatGroup $chatGroup)
    {
        $this->chatGroup = $chatGroup;
        $this->getMessagesReference()->remove();
    }


    private function setLastMessage($messageUid)
    {
        $path = "{$this->getChatGroupsMessageReference()}/last_message_id";
        $reference = $this->getFirebaseDatabase()->getReference($path);
        $reference->set($messageUid);
    }


    private function getMessagesReference()
    {
        $path = "{$this->getChatGroupsMessageReference()}/messages";
        return $this->getFirebaseDatabase()->getReference($path);
    }


    private function getChatGroupsMessageReference()
    {
        return "/chat_groups_messages/{$this->chatGroup->id}";
    }


}
