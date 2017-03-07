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
        'code', 'loan_date', 'outstanding', 'rate', 'period'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['loan_date', 'created_at', 'updated_at'];

    /**
     * Get the sureties that uses by the loan.
     */
    public function sureties() {
        return $this->hasMany(LoanSurety::class);
    }

    /**
     * Get the payments that uses by the loan.
     */
    public function payments() {
        return $this->hasMany(LoanPayment::class);
    }

    /**
     * Scope a query to expect inactive loan.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('outstanding', '>', $query->payments->sum('principle'));
    }

    /**
     * Scope a query to expect inactive loan.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFinished($query)
    {
        return $query->where('outstanding', $query->payments->sum('principle'));
    }
}
