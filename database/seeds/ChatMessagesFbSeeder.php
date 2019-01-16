<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use CodeShopping\Firebase\ChatMessageFb;
use Illuminate\Support\Collection;
use CodeShopping\Models\ChatGroup;
use Faker\Factory as FakerFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Seeder;
use CodeShopping\Models\User;


class ChatMessagesFbSeeder extends Seeder
{

    /**
     * @var Collection
     */
    private $allFakerFiles;
    private $fakerFilesPath = 'app/faker/chat_message_files';
    protected $numMessages = 10;


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->allFakerFiles = $this->getFakerFiles();

        /** @var EloquentCollection $chatGroups */
        $chatGroups = $this->getChatGroups();
        $users = User::all();
        $chatMessage = new ChatMessageFb();
        $self = $this;

        $chatGroups->each(function ($group) use($users, $chatMessage, $self){
            // Remover as a msg ref ao grupo
            $chatMessage->deleteMessages($group);

            foreach (range(1, $self->numMessages) as $value) {
                $textOrFile = rand(1, 10) % 2 == 0 ? 'text' : 'file';
                if ($textOrFile == 'text') {
                    $content = FakerFactory::create()->sentence(3);
                    $type = 'text';
                } else {
                    $content = $self->getUploadedFile();
                    $type = $content->getExtension() === 'wav' ? 'audio' : 'image';
                }


                $chatMessage->create([
                    'chat_group' => $group,
                    'content' => $content,
                    'type' => $type,
                    'firebase_uid' => $users->random()->profile->firebase_uid
                ]);
            }
        });
    }

    /**
     * @return EloquentCollection|static[]
     */
    protected function getChatGroups()
    {
        return ChatGroup::all();
    }

    /**
     * @return Collection
     */
    private function getFakerFiles(): Collection
    {
        $path = storage_path($this->fakerFilesPath);
        return collect(\File::allFiles($path));
    }

    /**
     * @return UploadedFile
     */
    private function getUploadedFile()
    {
        /** @var SplFileInfo $photoFile */
        $photoFile = $this->allFakerFiles->random();
        $uploadFile = new UploadedFile(
            $photoFile->getRealPath(),
            str_random(16) . '.' . $photoFile->getExtension()
        );
        //Upload da Foto
        return $uploadFile;
    }
}
