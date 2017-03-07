<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Background extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'backgrounds';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'background_date', 'url', 'copyright', 'copyrightlink'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['photo_date', 'created_at', 'updated_at'];
}
