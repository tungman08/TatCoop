<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bailsman extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bailsmans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_type_id', 'self_type', 'self_rate', 'self_maxguaruntee', 'self_netsalary', 'other_type', 'other_rate', 'other_maxguaruntee', 'other_netsalary'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the employee type that owns the bailsman.
     */
    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class);
    }
}
