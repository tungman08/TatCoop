<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dividendmember extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
     protected $table = 'dividend_member';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dividend_name', 'shareholding', 'shareholding_dividend', 'interest', 'interest_dividend'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
}
