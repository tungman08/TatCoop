<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewsAttachment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'news_attachments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'news_id', 'attach_type', 'file', 'display',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the news that uses by the attachment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function news() {
        return $this->belongsTo(News::class);
    }

    /**
     * Scope a query to expect attachment.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePhoto($query)
    {
        return $query->where('attach_type', 'photo')->get();
    }

    /**
     * Scope a query to expect attachment.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDocument($query)
    {
        return $query->where('attach_type', 'document')->get();
    }
}
