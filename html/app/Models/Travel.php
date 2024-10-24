<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute as CastsAttribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;


class Travel extends Model
{
    use HasFactory , HasUuids;
    use Sluggable;

    protected $tables = 'travel';

    protected $fillable = [
        'id',
        'is_public',
        'slug',
        'name',
        'description',
        'number_of_days',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ]
        ];
    }

    public function numberOfNights(): CastsAttribute
    {
        return CastsAttribute::make(
            get: fn ($value, $attributes) => $attributes['number_of_days'] - 1
        );
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }
    
    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }
}
