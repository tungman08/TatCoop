<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'winners';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reward_id', 'member_id', 'status'
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
    public function reward() {
        return $this->belongsTo(Reward::class);
    }

    /**
     * Get the member of the winner.
     */
    public function member() {
        return $this->belongsTo(Member::class);
    }
}
