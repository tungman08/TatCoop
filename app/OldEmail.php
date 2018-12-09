<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldEmail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'old_emails';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'email', 'canceled_at', 'remark'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['canceled_at', 'created_at', 'updated_at'];

    /**
     * Get the user of the old email.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }
}