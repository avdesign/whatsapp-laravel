<?php

use Illuminate\Database\Seeder;
use CodeShopping\Models\User;
use Illuminate\Database\Eloquent\Model;
use CodeShopping\Models\UserProfile;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \File::deleteDirectories('storage/app/public/users/photos', true);
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
            });
        factory(User::class, 50)
            ->create([
                'role' => User::ROLE_CUSTUMER
            ]);

    }

    public function getAdminPhoto()
    {
        return new \Illuminate\Http\UploadedFile(
            storage_path('app/faker/users_admin/anselmo.jpg'),
            str_random(16) . '.jpg'
        );
    }


}
