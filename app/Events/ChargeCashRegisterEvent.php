<?php

namespace App\Events;

use App\Transaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChargeCashRegisterEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $transaction;
    public $transaction_details;

    /**
     * Create a new event instance.
     *
     * @param Transaction $transaction
     * @param $transaction_details
     */
    public function __construct(Transaction $transaction, $transaction_details)
    {
        $this->transaction = $transaction;
        $this->transaction_details = $transaction_details;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
