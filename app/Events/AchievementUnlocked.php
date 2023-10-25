<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AchievementUnlocked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;

    public string $achievement_name;

    public function __construct(User $user, string $achievement_name)
    {
        $this->user = $user;
        $this->achievement_name = $achievement_name;
    }
}
