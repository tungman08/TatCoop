<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShareholdingType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'shareholding_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the shares that uses by the type.
     */
    public function shareHoldings() {
        return $this->hasMany(ShareHolding::class);
    }

    /**
     * Get the tempolary shares that uses by the type.
     */
    public function tempShareholdings() {
        return $this->hasMany(RoutineShareholdingDetail::class);
    }
}
