<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'employees';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Get the profile that uses by the employee.
     */
    public function profile() {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the employee type that uses by the employee.
     */
    public function employee_type() {
        return $this->belongsTo(EmployeeType::class);
    }
}
