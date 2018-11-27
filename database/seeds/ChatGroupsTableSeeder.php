<?php
declare(strict_types=1);

use CodeShopping\Models\ChatGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ChatGroupsTableSeeder extends Seeder
{
    /**
     * @var Collection
     */
    private $allFakePhotos;
    private $fakePhotosPath = 'app/faker/chat_groups';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->allFakePhotos = $this->getFakePhotos();
        /** @var Collection */
        $this->deteleAllChatGroupsPath();
        $self = $this;
        factory(ChatGroup::class, 10)
            ->make()
            ->each(function ($group) use($self) {
                ChatGroup::createWithPhoto([
                    'name' => $group->name,
                    'photo' => $self->getUploadedFile()
                ]);
            });

    }


    private function getUploadedFile()
    {
        /** @var SplFileInfo $photoFile */
        $photoFile = $this->allFakePhotos->random();
        $uploadFile = new \Illuminate\Http\UploadedFile(
            $photoFile->getRealPath(),
            str_random(16) . '.' . $photoFile->getExtension()
        );
        return $uploadFile;
    }

    private function getFakePhotos(): Collection
    {
        $path = storage_path($this->fakePhotosPath);
        return collect(\File::allFiles($path));
    }

    private function deteleAllChatGroupsPath()
    {
        $path = ChatGroup::CHAT_GROUP_PHOTO_PATH;
        \File::deleteDirectory(storage_path($path), true); // true não remover o dir
    }



}
