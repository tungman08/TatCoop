<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'document_type_id', 'display', 'file', 'position'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the type of the document.
     */
    public function document_type() {
        return $this->belongsTo(DocumentType::class);
    }   

    /**
     * Get the carousel of the document.
     */
    public function carousel() {
        return $this->hasOne(Carousel::class);
    }  
}
