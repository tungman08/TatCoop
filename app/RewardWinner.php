<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RewardWinner extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reward_winners';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reward_config_id', 'member_id', 'status'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
 
    /**
     * Get the reward of the winner.
     */
    public function rewardConfig() {
        return $this->belongsTo(RewardConfig::class);
    }

    /**
     * Get the member of the winner.
     */
    public function member() {
        return $this->belongsTo(Member::class);
    }
}
