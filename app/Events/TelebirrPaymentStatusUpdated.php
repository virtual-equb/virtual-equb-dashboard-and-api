<?php

namespace App\Events;

use App\Models\Payment;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class TelebirrPaymentStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->payment->member_id);
    }

    public function broadcastAs()
    {
        return 'telebirr-transaction-status-updated';
    }

    public function broadcastWith()
    {
        return [
            'payment_id' => $this->payment->id,
            'member_id' => $this->payment->member_id,
            'equb_id' => $this->payment->equb_id,
            'status' => $this->payment->status,
            'amount' => $this->payment->amount,
            'message' => 'Telebirr payment status updated ',
        ];
    }
}