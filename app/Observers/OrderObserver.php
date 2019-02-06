<?php

namespace CodeShopping\Observers;


use CodeShopping\Firebase\NotificationType;
use CodeShopping\Mail\OrderCreatedMail;
use CodeShopping\Models\Order;
use Illuminate\Support\Facades\Mail;

class OrderObserver
{
    /**
     * Enviar email
     * Não permite enviar emails quando rodar no terminal ou testes unitários
     */
    public function created(Order $order)
    {
        if (!$this->runningInTerminal()){
            $user = $order->user;
            Mail::to($user)->send(new OrderCreatedMail($order));
        }
    }


    public function updated(Order $order)
    {
        $this->handleIfPending($order);
        $this->handleIfApproved($order);
        $this->handleIfSent($order);
        $this->handleIfCancelled($order);
    }


    public function handleIfPending(Order $order)
    {
        if (Order::STATUS_PENDING != $order->status) {
            return;
        }
        /** Verifico se o user tem um token */
        $token = $order->user->profile->device_token;
        if (!$token || $this->runningInTerminal()){
            return;
        }
        /** verifica o campo  original payment_link */
        $oldPaymentLink = $order->getOriginal('payment_link');

        if ($order->payment_link && $order->payment_link !== $oldPaymentLink) {
            /** @var CloudMessagingFb $messaging */
            $messaging = app(CloudMessagingFb::class);
            $messaging
                ->setTitle("Link de pagamento do pedido.")
                ->setBody("Acesse o app para pagar seu pedido")
                ->setTokens([$token])
                ->setData([
                    'type' => NotificationType::ORDER_DO_PAYMENT,
                    'order' => $order->id
                ])
                ->send();
        }
    }

    public function handleIfApproved(Order $order)
    {
        if (Order::STATUS_APPROVED != $order->status) {
            return;
        }
        /** Verifico se o user tem um token */
        $token = $order->user->profile->device_token;
        if (!$token || $this->runningInTerminal()){
            return;
        }

        $oldStatus = $order->getOriginal('status');
        if ($oldStatus !== $order->status) {
            /** @var CloudMessagingFb $messaging */
            $messaging = app(CloudMessagingFb::class);
            $messaging
                ->setTitle("Seu pedido foi aprovado.")
                ->setBody("Em breve o produto {$order->product->name} será enviado.")
                ->setTokens([$token])
                ->setData([
                    'type' => NotificationType::ORDER_APPROVED,
                    'order' => $order->id
                ])
                ->send();
        }
    }

    public function handleIfCancelled(Order $order)
    {
        if (Order::STATUS_CANCELLED != $order->status) {
            return;
        }

        $oldStatus = $order->getOriginal('status');
        if ($oldStatus == Order::STATUS_SENT) {
            /**
             * lookForUpdate()
             * informa ao bd que o produto esta bloqueado enquanto não termna a transação
             */
            $product =$order->product()->lookForUpdate()->first();
            $product->increaseStock($order->amount);
        }
    }

    public function handleIfSent(Order $order)
    {
        if (Order::STATUS_SENT != $order->status) {
            return;
        }

        $oldStatus = $order->getOriginal('status');
        if ($oldStatus !== $order->status) {
            /**
             * lookForUpdate()
             * informa ao bd que o produto esta bloqueado enquanto não termna a transação
             */
            $product =$order->product()->lookForUpdate()->first();
            $product->decreaseStock($order->amount);

            /** Verifico se o user tem um token */
            $token = $order->user->profile->device_token;
            if (!$token || $this->runningInTerminal()){
                return;
            }

            /** @var CloudMessagingFb $messaging */
            $messaging = app(CloudMessagingFb::class);
            $messaging
                ->setTitle("Seu produto {$order->product->name} enviado.")
                ->setBody("Acesse o app para mais informações.")
                ->setTokens([$token])
                ->setData([
                    'type' => NotificationType::ORDER_SENT,
                    'order' => $order->id
                ])
                ->send();
        }
    }

    /**
     * Verivica se as requisições estão rodando no terminal (seeder) ou testes unitários
     * @return bool
     */
    private function runningInTerminal()
    {
        return app()->runningInConsole() || app()->runningUnitTests;
    }


}