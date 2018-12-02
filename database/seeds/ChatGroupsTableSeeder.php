<?php
declare(strict_types=1);
use CodeShopping\Models\ChatGroup;
use CodeShopping\Models\User;
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
        // Criar relacionamento do cliente padrão ao grupos
        $customerDefault = User::whereEmail('customer@user.com')->first();
        // Não permitir outros clientes a não seja que padrão  poderia passar outros também
        /** @var \Illuminate\Database\Eloquent\Collection $otherCustomers */
        $otherCustomers = User::whereRole(User::ROLE_CUSTOMER)
                ->whereNotIn('id', [$customerDefault->id])->get();

        factory(ChatGroup::class, 10)
            ->make()
            ->each(function ($group) use ($self, $otherCustomers) {
                $group = ChatGroup::createWithPhoto([
                    'name' => $group->name,
                    'photo' => $self->getUploadedFile()
                ]);
                // Pega coleção aleatória com 10
                // Transforma a coleção em outra coleção, somente com os ids
                // Transforma em um array nativo do php
                $customersId = $otherCustomers
                    ->random(10)
                    ->pluck('id')->toArray();

                // Pega o group criado e chama o relacionamento com attach adiciona o array de customers
                $group->users()->attach($customersId);
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