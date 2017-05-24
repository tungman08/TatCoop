<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dividend extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'dividends';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rate_year', 'sharholding_rate', 'loan_rate'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
}
