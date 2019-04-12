<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'confirmed', 'newaccount',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the member profile of the user.
     */
    public function member() {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the user confirmation of the user.
     */
    public function user_confirmation() {
        return $this->hasOne(UserConfirmation::class);
    }

    /**
     * Get the statistics for the user login.
     */
    public function user_statistics() {
        return $this->hasMany(UserStatistic::class);
    }

    /**
     * Get the histories for the user action.
     */
    public function user_histories() {
        return $this->hasMany(UserHistory::class);
    }

    /**
     * Get the theme profile of the user.
     */
    public function theme() {
        return $this->belongsTo(Theme::class);
    }

    /**
     * Get the old emails of the user.
     */
    public function old_emails() {
        return $this->hasMany(OldEmail::class);
    }

    /**
     * Set the user's password.
     *
     * @param  string  $value
     * @return string
     */
    public function setPasswordAttribute($value) {
        $this->attributes['password'] = bcrypt($value);
    }
}
