<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'districts';

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
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the province of the district.
     */
    public function province() {
        return $this->belongsTo(Province::class);
    }

    /**
     * Get the subdistricts for the district.
     */
    public function subdistricts() {
        return $this->hasMany(SubDistrict::class);
    }
}
