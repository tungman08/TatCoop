<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Diamond;

class VisitorStatistic extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'session', 'ip_address',
    ];

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

    /**
     * Get the session's create date.
     *
     * @param  string  $value
     * @return string
     */
    public function getCreateAtAttribute($value) {
        return Diamond::parse($value)->thai_format('j M Y H:i น.');
    }
}
