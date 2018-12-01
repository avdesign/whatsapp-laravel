<?php

use Illuminate\Database\Seeder;
use CodeShopping\Models\User;
use Illuminate\Database\Eloquent\Model;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \File::deleteDirectory(\CodeShopping\Models\UserProfile::photoPath(), true);
        factory(User::class, 1)
            ->create([
                'email' => 'admin@user.com',
                'role' => User::ROLE_SELLER
            ])
            ->each(function ($user){
                Model::reguard();
                $user->updateWithProfile([
                    'phone_number' => '+16505551231',
                    'photo' => $this->getAdminPhoto()
                ]);
                Model::unguard();
                // Salvar  o id do user do Firebase
                $user->profile->firebase_uid = 'EgzAu3ep4rcpXIUDkJNyiCWqp433';
                $user->profile->save();
            });
        factory(User::class, 1)
            ->create([
                'email' => 'customer@user.com',
                'role' => User::ROLE_CUSTUMER
            ])
            ->each(function ($user) {
                Model::reguard();
                $user->updateWithProfile([
                    'phone_number' => '+16505551232',
                    'photo' => $this->getAdminPhoto()
                ]);
                Model::unguard();
                // Salvar  o id do user do Firebase
                $user->profile->firebase_uid = 'sjCCUOLpJkewgNK9oujrK0CGjnC2';
                $user->profile->save();
            });
        factory(User::class, 20)
            ->create([
                'role' => User::ROLE_CUSTUMER
            ])->each(function ($user, $key){
                // Criar número de telefone aleatório
                $user->profile->phone_number = "+165055512{$key}";
                $user->profile->firebase_uid = "user-{$key}";
                $user->profile->save();
            });

    }

    public function getAdminPhoto()
    {
        return new \Illuminate\Http\UploadedFile(
            storage_path('app/faker/users_admin/anselmo.jpg'),
            str_random(16) . '.jpg'
        );
    }


}
