<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Diamond;

class RoutineShareholding extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'routine_shareholdings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'calculated_date', 'saved_date', 'approved_date'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['calculated_date', 'saved_date', 'approved_date', 'created_at', 'updated_at'];

    /**
     * Get the members associated with the routine.
     */
    public function details() {
        return $this->hasMany(RoutineShareholdingDetail::class, 'routine_shareholding_id', 'id');
    }
 
    public function getNameAttribute() {
        return Diamond::parse($this->calculated_date)->thai_format('F Y');
    }
}
