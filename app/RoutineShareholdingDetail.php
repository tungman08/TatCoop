<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoutineShareholdingDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'routine_shareholding_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'routine_shareholding_id', 'member_id', 'status', 'pay_date', 'amount',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['pay_date', 'created_at', 'updated_at'];

    /**
     * Get the routine that uses by the tempolary shareholding.
     */
    public function routine() {
        return $this->belongsTo(RoutineShareholding::class, 'routine_shareholding_id');
    }

    /**
     * Get the member that uses by the share.
     */
    public function member() {
        return $this->belongsTo(Member::class);
    }
}
