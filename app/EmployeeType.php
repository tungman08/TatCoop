<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'employee_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the employees for the type.
     */
    public function employees() {
        return $this->hasMany(Employee::class);
    }    
}
