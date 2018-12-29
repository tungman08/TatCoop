<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shareholding extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'shareholdings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id', 'shareholding_type_id', 'pay_date', 'amount', 'remark',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['pay_date', 'created_at', 'updated_at'];

    /**
     * Get the member that uses by the share.
     */
    public function member() {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the type that uses by the share.
     */
    public function shareholding_type() {
        return $this->belongsTo(ShareholdingType::class);
    }

    /**
     * Get the attach files that uses by the shareholding.
     */
    public function attachments() {
        return $this->hasMany(ShareholdingAttachment::class);
    }
}
