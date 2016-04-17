<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'members';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start_date', 'leave_date', 'shareholding_date',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['start_date', 'leave_date', 'shareholding_date', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Get the profile that uses by the member.
     */
    public function profile() {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the user for the member.
     */
    public function user() {
        return $this->hasOne(User::class);
    }

    /**
     * Scope a query to expect inactive member.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->whereNull('leave_date');
    }
}
