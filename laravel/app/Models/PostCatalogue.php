<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PostCatalogue extends Model
{
    use HasFactory;
    protected $table = 'post_catalogues';
    protected $fillable = [
        'name',
        'description',
        'canonical',
        'meta_title',
        'meta_description',
        'publish',
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->canonical = self::generateUniqueSlug($model->name);
        });

        static::updating(function ($model) {
            $model->canonical = self::generateUniqueSlug($model->name, $model->id);
        });
    }

    public static function generateUniqueSlug($name, $excludeId = null)
    {
        $canonical = Str::slug($name);
        $originalCanonical = $canonical;
        $count = 1;

        while (
            self::where('canonical', $canonical)
                ->where('id', '!=', $excludeId)
                ->exists()
        ) {
            $canonical = "{$originalCanonical}-" . $count++;
        }

        return $canonical;
    }

    public function childrens()
    {
        return $this->hasMany(PostCatalogue::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(PostCatalogue::class, 'parent_id');
    }

    public function scopeWithChildren($query)
    {
        return $query->with('children');
    }
}
