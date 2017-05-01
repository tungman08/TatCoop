<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'billings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'manager', 'treasurer'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
}
