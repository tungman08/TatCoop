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
        'admin_id', 'session'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the winners associated with the reward.
     */
    public function winners() {
        return $this->hasMany(Winner::class);
    }

    /**
     * Get the admin of the reward.
     */
    public function admin() {
        return $this->belongsTo(Administrator::class);
    }
}
