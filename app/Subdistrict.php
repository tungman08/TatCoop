<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subdistrict extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'subdistricts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    
    /**
     * Get the district of the subdistrict.
     */
    public function district() {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the postcode of the subdistrict.
     */
    public function postcode() {
        return $this->belongsTo(Postcode::class);
    }
}
