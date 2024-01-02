<?php

namespace App\Events;

use App\Models\Training;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserFinishedTraining
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $training;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Training $training)
    {
        $this->user = $user;
        $this->training = $training;
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
