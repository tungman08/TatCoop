<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Administrator extends Authenticatable
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'administrators';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id', 'name', 'email', 'password', 'password_changed',
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
     * Get the role that uses by the administrators.
     */
    public function role() {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the rewards for the administrators.
     */
    public function rewards() {
        return $this->hasMany(Reward::class, 'admin_id');
    }

    /**
     * Get the statistics for the administrators login.
     */
    public function administrator_statistics() {
        return $this->hasMany(AdministratorStatistic::class);
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

    /**
     * Scope a query to only include super admin.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuper($query)
    {
        return $query->where('role_id', 1);
    }

    /**
     * Scope a query to only include admin.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmin($query)
    {
        return $query->where('role_id', 2);
    }

    /**
     * Scope a query to only include viewer.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeViewer($query)
    {
        return $query->where('role_id', 3);
    }

    public function getFullnameAttribute() {
        return $this->attributes['name'] .' '. $this->attributes['lastname'];
    }
}
