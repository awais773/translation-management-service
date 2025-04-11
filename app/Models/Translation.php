<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = ['group', 'key', 'value', 'locale_id'];

    public function locale()
    {
        return $this->belongsTo(Locale::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function scopeWithFilters($query, $filters)
    {
        return $query->when(isset($filters['search']), function ($query) use ($filters) {
            $query->where('key', 'like', '%'.$filters['search'].'%')
                  ->orWhere('value', 'like', '%'.$filters['search'].'%');
        })
        ->when(isset($filters['tags']), function ($query) use ($filters) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->whereIn('name', $filters['tags']);
            });
        })
        ->when(isset($filters['locale']), function ($query) use ($filters) {
            $query->whereHas('locale', function ($q) use ($filters) {
                $q->where('code', $filters['locale']);
            });
        });
    }
}