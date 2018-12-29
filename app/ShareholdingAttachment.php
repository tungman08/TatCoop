<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShareholdingAttachment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shareholding_attachments';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shareholding_id', 'file', 'display',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the shareholding that uses by the attach file.
     */
    public function shareholding() {
        return $this->belongsTo(Shareholding::class);
    }
}
