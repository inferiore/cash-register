<?php

namespace App\Listeners;

use App\CashRegisterBalance;
use App\Events\ChargeCashRegisterEvent;
use App\Support\Types\TransactionTypes;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class CalculateMoneyListener
{
    /**
     * Create the event listener.
     *
     * @param CashRegisterBalance $cashRegister
     */
    protected $cashRegister;
    public function __construct(CashRegisterBalance $cashRegister)
    {
        $this->cashRegister = $cashRegister;
    }

    /**
     * Handle the event.
     *
     * @param ChargeCashRegisterEvent $event
     * @return void
     */
    public function handle(ChargeCashRegisterEvent $event)
    {

        if($event->transaction->transaction_type == TransactionTypes::Accredit){
            $this->cashRegister->accredit($event->transaction_details);
        }
        if($event->transaction->transaction_type == TransactionTypes::Deduct){
            $this->cashRegister->deduct($event->transaction_details);
        }

    }
}
