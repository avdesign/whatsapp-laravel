<?php
/**
 * Responsavél pelo envio de mensagem do site
 * User: avdesign
 * Date: 02/12/18
 * Time: 17:01
 */


namespace CodeShopping\Firebase;

use CodeShopping\Models\ChatGroup;
use Illuminate\Http\UploadedFile;


class ChatMessageFb
{
    use FirebaseSync;

    private $chatGroup;

    public function create(array $data){

        $this->chatGroup = $data['chat_group'];
        $type = $data['type'];

        switch ($type) {
            case 'audio':
            case 'image':
                $this->upload($data['content']);
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $data['content'];
                $fileUrl = $this->groupFilesDir() . '/' . $uploadedFile->hashName();
                $data['content'] = $fileUrl;
        }
        $reference = $this->getMessagesReference();
        $reference->push([
            'type' => $data['type'],
            'content' => $data['content'],
            'created_at' => ['.sv' => 'timestamp'], // firebase gera timestamp
            'user_id' => $data['firebase_uid']
        ]);


    }

    private function upload(UploadedFile $file)
    {
        $file->store($this->groupFilesDir(), ['disk' => 'public']);


    }


    /**
     * Pasta onde salvar os files
     * @return string
     */
    private function groupFilesDir()
    {
        return ChatGroup::DIR_CHAT_GROUPS . '/' . $this->chatGroup->id . '/messages_files';
    }


    /**
     * Passo a Reference do chat_groups
     * @return \Kreait\Firebase\Database\Reference
     */
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