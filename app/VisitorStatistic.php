<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitorStatistic extends Model
{
    use SoftDeletes;
    /**

     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'visitor_statistics';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'session', 'ip_address',
    ];

    /**
     * The attributes that are timestamps.
     *
     * @var array
     */
    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'deleted_at'];

    /**
     * Get the platform that uses by the session.
     */
    public function platform() {
        return $this->belongsTo(Platform::class);
    }


    /**
     * Get the browser that uses by the session.
     */
    public function browser() {
        return $this->belongsTo(Browser::class);
    }
}
