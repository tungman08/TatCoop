<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Diamond;

class LoanType extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'loan_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'rate', 'start_date', 'expire_date',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['start_date', 'expire_date', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Get the limits that uses by the type.
     */
    public function limits() {
        return $this->hasMany(LoanTypeLimit::class);
    }
    
    /**
     * Scope a query to only include normal province.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->whereDate('expire_date', '>=', Diamond::today())
            ->whereNull('deleted_at');
    }

    /**
     * Scope a query to only include normal province.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSpecial($query)
    {
        return $query->where('id', '>', 2)
            ->whereDate('expire_date', '>=', Diamond::today())
            ->whereNull('deleted_at');
    }

    /**
     * Scope a query to only include normal province.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->whereDate('expire_date', '<', Diamond::today())
            ->whereNull('deleted_at');
    }

    /**
     * Scope a query to only include normal province.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDeletedType($query)
    {
        return $query->whereNotNull('deleted_at');
    }
}
