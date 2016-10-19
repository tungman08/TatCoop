<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prefix extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'prefixs';

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
     * Get the members associated with the prefix.
     */
    public function profiles() {
        return $this->hasMany(Profile::class);
    }
}
