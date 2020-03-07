<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'members';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'profile_id', 'shareholding', 'fee', 'start_date', 'leave_date',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['start_date', 'leave_date', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Get the profile that uses by the member.
     */
    public function profile() {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the loans that uses by the member.
     */
    public function loans() {
        return $this->hasMany(Loan::class);
    }

    /**
     * Get the shares that uses by the member.
     */
    public function shareHoldings() {
        return $this->hasMany(ShareHolding::class);
    }

    /**
     * Get the tempolary shares that uses by the member.
     */
    public function tempShareholdings() {
        return $this->hasMany(RoutineShareholdingDetail::class);
    }

    /**
     * Get the winners that uses by the member.
     */
    public function reward_winners() {
        return $this->hasMany(RewardWinner::class);
    }

    /**
     * Get the reward that uses by the member.
     */
    public function reward() {
        return $this->belongsToMany(Reward::class)
            ->withTimestamps();
    }

    /**
     * Get the user for the member.
     */
    public function user() {
        return $this->hasOne(User::class);
    }

    /**
     * Get the beneficiaries for the member.
     */
    public function beneficiaries() {
        return $this->hasMany(Beneficiary::class);
    }

    /**
     * Get the loan sureties that uses by the member.
     */
    public function sureties() {
        return $this->belongsToMany(Loan::class)
            ->withPivot('amount', 'yourself', 'salary')
            ->withTimestamps();
    }

    /**
     * Get the dividends that uses by the members.
     */
    public function dividends() {
        return $this->belongsToMany(Member::class)
            ->withPivot('dividend_name', 'shareholding', 'shareholding_dividend', 'interest', 'interest_dividend')
            ->withTimestamps();
    }

    /**
     * Scope a query to expect inactive member.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->whereNull('leave_date');
    }

    public function scopeInactive($query)
    {
        return $query->whereNotNull('leave_date');
    }

    public function getMemberCodeAttribute()
    {
        return str_pad($this->attributes['id'], 5, "0", STR_PAD_LEFT);
    }
}
