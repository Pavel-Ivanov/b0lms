<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = [
        'name',
        'announcement',
        'description',
        'duration',
        'is_published',
        'course_type_id',
        'course_level_id',
        'course_category_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function courseType(): BelongsTo
    {
        return $this->belongsTo(CourseType::class);
    }

    public function courseLevel(): BelongsTo
    {
        return $this->belongsTo(CourseLevel::class);
    }

    public function courseCategory(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class);
    }


}
