<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AddEventLogs
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $type;
    public $user_id;
    public $text;
    public $ip;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($type,$user_id,$text,$ip=null)
    {
        $this->type = $type;
        $this->user_id = $user_id;
        $this->text = $text;
        $this->ip = $ip;
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
