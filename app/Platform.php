<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'platforms';

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
     * Get the administrator statistics for the platform.
     */
    public function administrator_statistics()
    {
        return $this->hasMany(AdministratorStatistic::class);
    }

    /**
     * Get the user statistics for the platform.
     */
    public function user_statistics()
    {
        return $this->hasMany(UserStatistic::class);
    }

    /**
     * Get the visitor statistics for the platform.
     */
    public function visitor_statistics()
    {
        return $this->hasMany(VisitorStatistic::class);
    }
}
