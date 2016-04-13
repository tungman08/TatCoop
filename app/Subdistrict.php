<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubDistrict extends Model
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
     * Get the district of the subdistrict.
     */
    public function district() {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the postcode associated with the subdistrict.
     */
    public function postcode() {
        return $this->hasOne(Postcode::class);
    }
}
