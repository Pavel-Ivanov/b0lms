<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CompanyDepartment extends Model
{
    use LogsActivity;

   protected $fillable = [
       'name',
       'description',
       'is_published'
   ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

}
