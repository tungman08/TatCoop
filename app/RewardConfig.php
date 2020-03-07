<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RewardConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reward_configs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reward_id', 'price', 'amount', 'register', 'special'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the reward of the config.
     */
    public function reward() {
        return $this->belongsTo(Reward::class);
    }
    
    /**
     * Get the winners associated with the config.
     */
    public function rewardWinners() {
        return $this->hasMany(RewardWinner::class);
    }
}
