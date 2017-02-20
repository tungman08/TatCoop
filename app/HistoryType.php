<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoryType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'history_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'icon', 'color',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the user histories for the type.
     */
    public function user_histories() {
        return $this->hasMany(UserHistory::class);
    } 

    /**
     * Get the admin histories for the type.
     */
    public function admin_histories() {
        return $this->hasMany(AdminHistory::class);
    }       
}
