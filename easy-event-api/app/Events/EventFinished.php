<?php

namespace App\Events;

use App\Models\Evenement;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventFinished
{
    use Dispatchable, SerializesModels;

    public $evenement;

    public function __construct(evenement $evenement)
    {
        $this->evenement = $evenement;
    }
}
