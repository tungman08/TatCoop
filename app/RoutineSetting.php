<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoutineSetting extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'routine_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'calculate_status', 'approve_status', 'save_status'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
}
