<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Postcode extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'postcodes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
    
    /**
     * Get the subdistrict associated with the postcode.
     */
    public function subdistricts() {
        return $this->hasMany(Subdistrict::class);
    }
}
