<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanTypeLimit extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'loan_type_limits';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'loan_type_id', 'cash_begin', 'cash_end', 'shareholding', 'surety', 'period',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the type that uses by the limit.
     */
    public function loanType() {
        return $this->belongsTo(LoanType::class);
    }
}
