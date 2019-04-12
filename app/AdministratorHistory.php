<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdministratorHistory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'administrator_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id', 'history_type_id', 'description',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the history type that uses by the administrator history.
     */
    public function history_type() {
        return $this->belongsTo(HistoryType::class);
    }

    /**
     * Get the admin that uses by the session.
     */
    public function admin() {
        return $this->belongsTo(Administrator::class, 'admin_id');
    }
}
