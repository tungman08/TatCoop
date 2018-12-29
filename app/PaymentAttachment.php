<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentAttachment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payment_attachments';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_id', 'file', 'display',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the payment that uses by the attach file.
     */
    public function payment() {
        return $this->belongsTo(payment::class);
    }
}
