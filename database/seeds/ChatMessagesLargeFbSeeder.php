<?php

class ChatMessagesLargeFbSeeder extends ChatMessagesFbSeeder
{
    protected $numMessages = 100;

    protected function getChatGroups()
    {
        return \CodeShopping\Models\ChatGroup::whereId(1)->get();
    }
}
