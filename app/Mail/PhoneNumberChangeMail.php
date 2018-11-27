<?php

namespace CodeShopping\Mail;

use CodeShopping\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PhoneNumberChangeMail extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * @var Url
     */
    public $url;
    /**
     * @var User
     */
    public $user;
    /**
     * @var
     */
    private $token;

    /**
     * Criar email personalizado
     * php artisan make:mail PhoneNumberChangeMail --markdown=mails.phone_number_change_mail
 *
     * @return void
     */
    public function __construct(User $user, $token)
    {
        //
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->url = route('customers.web_phone_number_update', ['token' => $this->token]);
        return $this
            ->subject('Alteração de número de telefone')
            ->markdown('mails.phone_number_change_mail');
    }
}
