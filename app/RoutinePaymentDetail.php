<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoutinePaymentDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'routine_payment_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'routine_payment_id', 'loan_id', 'status', 'pay_date', 'principle', 'interest'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['pay_date', 'created_at', 'updated_at'];

    /**
     * Get the routine that uses by the tempolary payment.
     */
    public function routine() {
        return $this->belongsTo(RoutinePayment::class, 'routine_payment_id');
    }

    /**
     * Get the loan that uses by the tempolary payment.
     */
    public function loan() {
        return $this->belongsTo(Loan::class);
    }
}
