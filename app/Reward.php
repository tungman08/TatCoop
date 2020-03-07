<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rewards';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id', 'session', 'reward_status_id'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the admin of the reward.
     */
    public function admin() {
        return $this->belongsTo(Administrator::class);
    }

    /**
     * Get the status of the reward.
     */
    public function rewardStatus() {
        return $this->belongsTo(RewardStatus::class);
    }

    /**
     * Get the configs associated with the reward.
     */
    public function rewardConfigs() {
        return $this->hasMany(RewardConfig::class);
    }

    /**
     * Get the register member associated with the reward.
     */
    public function members() {
        return $this->belongsToMany(Member::class)
            ->withTimestamps();
    }
}
