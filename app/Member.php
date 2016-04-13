<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Diamond;

class Member extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'citizen_code', 'employee_code', 'name', 'surname', 'address', 'birth_date', 'member_date', 'shareholding_date',
    ];

    /**
     * Get the prefix of the menber.
     */
    public function prefix() {
        return $this->belongsTo(Prefix::class);
    }

    /**
     * Get the subdistrict of the menber.
     */
    public function subdistrict() {
        return $this->belongsTo(Subdistrict::class);
    }

    /**
     * Get the postcode of the menber.
     */
    public function postcode() {
        return $this->belongsTo(Postcode::class);
    }

    /**
     * Get the member's create date.
     *
     * @param  string  $value
     * @return string
     */
    public function getCreateAtAttribute($value) {
        return Diamond::parse($value)->thai_format('j M Y');
    }
}
