<?php

namespace CodeShopping\Rules;

use CodeShopping\Models\Order;
use Illuminate\Contracts\Validation\Rule;

class OrderPaymentLinkChange implements Rule
{
    /**
     * @var
     */
    private $status;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($status)
    {
        $this->status = $status;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->status == Order::STATUS_PENDING;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O status precisa ser '.Order::STATUS_PENDING .' para atualizar o link de pagamento.';
    }
}
