<?php

namespace App\Achiever\Badges;

use App\Models\Achievement;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

abstract class BadgeType
{
    /**
     * Achievement's related model record
     *
     * @var Achievement
     */
    protected Model $model;

    public function __construct()
    {
        // Get or create the Achievement model
        // We use this property to identify the Achievement model.
        $this->model = Badge::firstOrCreate([
            'name' => $this->name(),
            'order_column' => $this->order(),
            'required_achievements' => $this->requiredAchievements(),
        ]);
    }

    /**
     * Qualify if the user can unlock the badge.
     */
    abstract public function qualifier(User $user): bool;

    /**
     * Get the human-readable badge name.
     */
    abstract public function name(): string;

    /**
     * Get the badge order number.
     */
    abstract public function order(): int;

    /**
     * Get the required achievements to unlock the badge.
     */
    abstract public function requiredAchievements(): int;

    /**
     * Get the badge model.
     */
    public function getModel(): Badge
    {
        return $this->model;
    }

    /**
     * Get the badge model ID.
     *
     * @return mixed
     */
    public function modelKey()
    {
        return $this->model->getKey();
    }
}
