<?php

use CodeShopping\Models\ChatGroup;
use CodeShopping\Models\ChatGroupInvitation;
use Illuminate\Database\Seeder;

class ChatGroupInvitationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $chatGroups = ChatGroup::all();

        factory(ChatGroupInvitation::class, 1)
            ->make()
            ->each(function ($invitation) use ($chatGroups){
                $invitation->group_id = $chatGroups->first()->id;
                $invitation->save();
            });

        factory(ChatGroupInvitation::class, 20)
            ->make()
            ->each(function ($invitation) use ($chatGroups){
                $invitation->group_id = $chatGroups->random()->id;
                $invitation->save();
            });
    }
}
