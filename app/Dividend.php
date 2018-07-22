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
    protected $dates = ['release_date', 'created_at', 'updated_at'];

    /**
     * Get the members that uses by the dividend.
     */
    public function members() {
        return $this->belongsToMany(Member::class)
            ->withPivot('dividend_name', 'shareholding', 'shareholding_dividend', 'interest', 'interest_dividend')
            ->withTimestamps();
    }
}
