<?php

namespace CodeShopping\Providers;


use CodeShopping\Models\ChatGroupInvitation;
use CodeShopping\Models\ChatInvitationUser;
use CodeShopping\Firebase\NotificationType;
use CodeShopping\Models\Order;
use CodeShopping\Models\Product;
use CodeShopping\Observers\OrderObserver;
use Illuminate\Support\ServiceProvider;

use CodeShopping\Models\ProductInput;
use CodeShopping\Models\ProductOutput;
use Kreait\Firebase;
use Kreait\Firebase\Factory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /** +stock */
        ProductInput::created(function($input){
            $product = $input->product;
            $product->increaseStock($input->amount);
        });
        /** -stock */
        ProductOutput::created(function($input){
            /** @var Product $product */
            $product = $input->product;
            $product->decreaseStock($input->amount);
        });

        // Executar depois de criado
        ChatGroupInvitation::creating(function ($invitation) {
           $invitation->slug = str_random(7);
           $invitation->remaining = $invitation->total;
        });

        // Executar depois de alterado
        ChatGroupInvitation::updating(function (ChatGroupInvitation $invitation) {
            //valor antigo
            $oldRemaining = $invitation->getOriginal('remaining');
            //valor novo
            $newRemaining = $invitation->remaining;
            if($oldRemaining == $newRemaining){
                $invitation->remaining = $invitation->total;
            }
        });

        //Reservar vaga no grupo
        ChatInvitationUser::created(function ($userInvitation) {
            $linkInvitation = $userInvitation->invitation;
            $linkInvitation->remaining -= 1;
            $linkInvitation->save();
        });

        ChatInvitationUser::updated(function ($userInvitation) {
            if ($userInvitation->status == ChatInvitationUser::STATUS_PENDING) {
                return;
            }

            if ($userInvitation->status == ChatInvitationUser::STATUS_REPROVED) {
                $linkInvitation = $userInvitation->invitation;
                $linkInvitation->remaining += 1;
                $linkInvitation->save();
            }

            $group = $userInvitation->invitation->group;
            $userId = $userInvitation->user->id;
            $group->users()->attach($userId);

            // push notification
            $token = $userInvitation->user->profile->device_token;
            if (!$token) {
                return;
            }
            /** @var CloudMessagingFb $messaging */
            $messaging = app(CloudMessagingFb::class);
            $messaging
                ->setTitle("Sua inscrição foi aprovada.")
                ->setBody("Você esta inscrito em um novo grupo.")
                ->setTokens([$token])
                ->setData([
                    'type' => NotificationType::CHAT_GROUP_SUBSRIBE,
                    'chat_group_name' => $group->name
                ])
                ->send();

            //push notificaton
        });

        /**
         * OrderObserver
         */
        Order::observe(OrderObserver::class);





    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Firebase::class, function(){
           $serviceAccount = Firebase\ServiceAccount::fromJsonFile(base_path('firebase-admin.json'));
           return (new Factory())->withServiceAccount($serviceAccount)->create();
        });
    }
}
