<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanPayment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'loan_payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'loan_id', 'pay_date', 'principle', 'interest'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['pay_date', 'created_at', 'updated_at'];

    /**
     * Get the loan that uses by the payment.
     */
    public function loan() {
        return $this->belongsTo(Loan::class);
    }
}
