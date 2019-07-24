<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'loan_id', 'payment_method_id', 'pay_date', 'period', 'principle', 'interest', 'remark'
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

    /**
     * Get the loan that uses by the payment.
     */
    public function PaymentMethod() {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get the attach files that uses by the payment.
     */
    public function attachments() {
        return $this->hasMany(PaymentAttachment::class);
    }
}
