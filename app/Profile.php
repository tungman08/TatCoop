<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'profiles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'citizen_code', 'name', 'lastName', 'address', 'birth_date',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['birth_date', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Get the members for the profile.
     */
    public function members()
    {
        return $this->hasMany(Member::class);
    }

    /**
     * Get the employees for the profile.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
