<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'profiles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'citizen_code', 'name', 'lastName', 'address', 'birth_date', 'subdistrict_id', 'district_id', 'province_id', 'postcode_id',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['birth_date', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Get the members for the profile.
     */
    public function members()
    {
        return $this->hasMany(Member::class);
    }

    /**
     * Get the employees for the profile.
     */
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * Get the profile that uses by the employee.
     */
    public function prefix() {
        return $this->belongsTo(Prefix::class);
    }

    public function subdistrict() {
        return $this->belongsTo(Subdistrict::class);
    }

    public function postcode() {
        return $this->belongsTo(Postcode::class);
    }

    /**
     * Scope a query to only include normal province.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query) {
        return $query->whereNull('leave_date');
    }

    public function getFullNameAttribute() {
        return $this->prefix->name . ' ' . $this->attributes['name'] .' '. $this->attributes['lastname'];
    }

    public function getFullAddressAttribute() {
        return $this->attributes['address'] . ' ' . 
            (($this->subdistrict->district->province['id'] == 1) ? 'แขวง'. $this->subdistrict['name'] : 'ต.' . $this->subdistrict['name']) . ' ' . 
            (($this->subdistrict->district->province['id'] == 1) ? 'เขต' . $this->subdistrict->district['name'] : 'อ.' . $this->subdistrict->district['name']) . ' ' . 
            (($this->subdistrict->district->province['id'] == 1) ? $this->subdistrict->district->province['name'] : 'จ.' . $this->subdistrict->district->province['name']) . ' ' . 
            $this->postcode['code'];
    }

    public function getCitizenCodeAttribute() {
        return (mb_substr($this->attributes['citizen_code'], 0, 1)  != '<') ? (mb_substr($this->attributes['citizen_code'], 0, 1) . '-' .
            mb_substr($this->attributes['citizen_code'], 1, 4) . '-' . mb_substr($this->attributes['citizen_code'], 5, 5) . '-' .
            mb_substr($this->attributes['citizen_code'], 10, 2) . '-' . mb_substr($this->attributes['citizen_code'], 12, 1)) :
            $this->attributes['citizen_code'];
    }
}
