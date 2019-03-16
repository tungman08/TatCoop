<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'loans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id', 'loan_type_id', 'payment_type_id', 'code', 'loaned_at', 'outstanding', 'rate', 'period', 'shareholding', 'completed_at', 'step'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['loaned_at', 'completed_at', 'created_at', 'updated_at'];

    /**
     * Get the loan sureties that uses by the loan.
     */
    public function sureties() {
        return $this->belongsToMany(Member::class)
            ->withPivot('amount', 'yourself', 'salary')
            ->withTimestamps();
    }

    public function loanType() {
        return $this->belongsTo(LoanType::class);
    }

    public function paymentType() {
        return $this->belongsTo(PaymentType::class);
    }

    /**
     * Get the payments that uses by the loan.
     */
    public function payments() {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the tempolary payments that uses by the loan.
     */
    public function tempPayments() {
        return $this->hasMany(RoutinePaymentDetail::class);
    }
    
    /**
     * Get the member that uses by the loan.
     */
    public function member() {
        return $this->belongsTo(Member::class);
    }

    /**
     * Scope a query to expect inactive loan.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('code')
            ->whereNull('completed_at');
    }

    /**
     * Scope a query to expect inactive loan.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFinished($query)
    {
        return $query->whereNotNull('code')
            ->whereNotNull('completed_at');
    }
}
