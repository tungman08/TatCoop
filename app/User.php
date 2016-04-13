<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Diamond;

class User extends Authenticatable
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password',
    ];

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
     * Get the statistics for the user login.
     */
    public function user_statistics() {
        return $this->hasMany(UserStatistic::class);
    }

    /**
     * Get the user's create date.
     *
     * @param  string  $value
     * @return string
     */
    public function getCreateAtAttribute($value) {
        return Diamond::parse($value)->thai_format('j M Y');
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
