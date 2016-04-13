<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class District extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

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
