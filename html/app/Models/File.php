<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'path',
        'fileable_id',
        'fileable_type'
    ];

    protected $hidden = ['fileable_id','fileable_type','created_at','updated_at'];

    /**
     * Get the parent commentable model (post or video).
     */
    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }
}
