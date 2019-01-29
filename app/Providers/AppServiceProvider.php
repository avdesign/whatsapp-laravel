<?php

namespace CodeShopping\Providers;

use CodeShopping\Models\ChatGroupInvitation;
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
