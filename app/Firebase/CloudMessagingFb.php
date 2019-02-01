<?php
/**
 * Created by PhpStorm.
 * User: avdesign
 * Date: 31/01/19
 * Time: 11:18
 */

namespace CodeShopping\Firebase;


use sngrl\PhpFirebaseCloudMessaging\Client;
use sngrl\PhpFirebaseCloudMessaging\Message;
use sngrl\PhpFirebaseCloudMessaging\Notification;
use sngrl\PhpFirebaseCloudMessaging\Recipient\Device;


class CloudMessagingFb
{
    private $title;

    private $body;

    private $tokens;


    public function send()
    {
        $client = new Client();
        $client->setApiKey(env('FB_SERVER_KEY'));

        $message = new Message();
        foreach ($this->tokens as $token) {
            $message->addRecipient(new Device($token));
        }

        $message->setNotification(
            new Notification($this->title, $this->body)
        );

        $client->send($message);

    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @param mixed $tokens
     */
    public function setTokens(array $tokens)
    {
        $this->tokens = $tokens;
    }



}