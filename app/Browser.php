<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Diamond;

class Browser extends Model
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
     * Get the administrator statistics for the browser.
     */
    public function administrator_statistics()
    {
        return $this->hasMany(AdministratorStatistic::class);
    }

    /**
     * Get the user statistics for the browser.
     */
    public function user_statistics()
    {
        return $this->hasMany(UserStatistic::class);
    }

    /**
     * Get the visitor statistics for the browser.
     */
    public function visitor_statistics()
    {
        return $this->hasMany(VisitorStatistic::class);
    }

    /**
     * Get the browser's create date.
     *
     * @param  string  $value
     * @return string
     */
    public function getCreateAtAttribute($value) {
        return Diamond::parse($value)->thai_format('j M Y H:i น.');
    }
}
