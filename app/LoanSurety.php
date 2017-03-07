<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanSurety extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'loan_sureties';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'loan_id', 'member_id', 'myself', 'amount'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the loan that uses by the surety.
     */
    public function loan() {
        return $this->belongsTo(Loan::class);
    }
}
