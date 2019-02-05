<?php

namespace CodeShopping\Rules;

use CodeShopping\Models\Order;
use Illuminate\Contracts\Validation\Rule;

class OrderStatusChange implements Rule
{
    /**
     * Status atual e quais serão permitidos para alterar.
     *
     */
    private $rulesChanges = [
        Order::STATUS_APPROVED => [Order::STATUS_SENT, Order::STATUS_CANCELLED],
        Order::STATUS_SENT => [Order::STATUS_CANCELLED],
        Order::STATUS_CANCELLED => [Order::STATUS_CANCELLED]
    ];

    /**
     * Status que tenho na order
     * @var
     */
    private $oldStatus;


    public function __construct($oldStatus)
    {
        $this->oldStatus = $oldStatus;
    }

    /**
     * Recebe o novo status
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //Verifica se está presente no array e se faz parte das regras (!).
        if (!array_key_exists($this->oldStatus, $this->rulesChanges)) {
            return true;
        }

        // Verifica se o valor passado esta nas regras (!)
        if (!in_array($value, $this->rulesChanges[$this->oldStatus])){
            return false;
        }

        return true;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Status inválido, com este valor só é permitido alterar para: '.
            (implode(',', $this->rulesChanges[$this->oldStatus]));
    }
}
