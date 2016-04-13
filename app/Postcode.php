<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Postcode extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code'
    ];

    /**
     * Get the subdistrict associated with the postcode.
     */
    public function subdistrict() {
        return $this->belongsTo(Postcode::class);
    }
}
