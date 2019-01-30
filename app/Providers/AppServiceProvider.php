<?php

namespace CodeShopping\Providers;

use CodeShopping\Models\ChatGroupInvitation;
use CodeShopping\Models\ChatInvitationUser;
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
        ProductInput::created(function($input){
            $product = $input->product;
            $product->stock += $input->amount;
            $product->save();
        });

        // Executa quando cria
        ProductOutput::created(function($output){
            $product = $output->product;
            $product->stock -= $output->amount;
            if($product->stock <0){
                throw new \Exception("Estoque de {$product->name} não pode ser negativo.");
            }
            $product->save();
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
        });


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
