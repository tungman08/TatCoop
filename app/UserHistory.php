<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserHistory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'user_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'history_type_id', 'description',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the history type that uses by the user history.
     */
    public function history_type() {
        return $this->belongsTo(HistoryType::class);
    }

    /**
     * Get the user that uses by the session.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }
}
