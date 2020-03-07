<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RewardStatus extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reward_statuses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the rewards associated with the status.
     */
    public function rewards() {
        return $this->hasMany(Reward::class);
    }
}
