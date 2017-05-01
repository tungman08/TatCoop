<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'payment_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public function loans() {
        return $this->hasMany(Loan::class);
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
    
    /**
     * Get the members associated with the type.
     */
    // public function profiles() {
    //     return $this->hasMany(Profile::class);
    // }
}
