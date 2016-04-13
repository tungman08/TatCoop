<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Diamond;

class Administrator extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
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
     * Get the statistics for the administrators login.
     */
    public function administrator_statistics() {
        return $this->hasMany(AdministratorStatistic::class);
    }

    /**
     * Get the administrator's create date.
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
